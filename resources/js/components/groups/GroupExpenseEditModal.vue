<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { computed, watch } from 'vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import Dialog from '@/components/ui/dialog/Dialog.vue';
import DialogContent from '@/components/ui/dialog/DialogContent.vue';
import DialogHeader from '@/components/ui/dialog/DialogHeader.vue';
import DialogTitle from '@/components/ui/dialog/DialogTitle.vue';
import DialogDescription from '@/components/ui/dialog/DialogDescription.vue';

type MemberUser = {
    id: string | number;
    name: string;
    email: string;
} | null;

type Member = {
    id: string;
    status: string;
    invite_email?: string | null;
    user?: MemberUser;
};

type ExpenseSplit = {
    id: string | number;
    amount: number;
    settled: boolean;
    user_id?: string | number | null;
    member_email?: string | null;
    user?: MemberUser;
};

type Expense = {
    id: string | number;
    description: string;
    amount: number;
    paid_by: string | number | null;
    payer_email?: string | null;
    payer?: MemberUser;
    splits: ExpenseSplit[];
};

const props = defineProps<{
    members: Member[];
    expense: Expense | null;
    modelValue: boolean;
}>();

const emit = defineEmits<{
    (e: 'update:modelValue', value: boolean): void;
}>();

const isOpen = computed({
    get: () => props.modelValue,
    set: (value: boolean) => emit('update:modelValue', value),
});

const memberOptions = computed(() =>
    props.members
        .map((member) => {
            const user = member.user;
            const email = user?.email ?? member.invite_email ?? null;

            if (user) {
                return {
                    key: `user:${user.id}`,
                    label: user.name || user.email,
                };
            }

            if (email) {
                return {
                    key: `email:${email}`,
                    label: email,
                };
            }

            return null;
        })
        .filter((option): option is { key: string; label: string } => option !== null),
);

const editForm = useForm<{
    description: string;
    amount: string;
    payerKey: string | null;
    splits: {
        memberKey: string | null;
        amount: string;
    }[];
}>({
    description: '',
    amount: '',
    payerKey: null,
    splits: [],
});

const manualTotal = computed(() =>
    editForm.splits.reduce(
        (total, split) => total + (parseFloat(split.amount || '0') || 0),
        0,
    ),
);

const splitMode = computed<'equal' | 'manual'>(() =>
    editForm.splits.length > 0 ? 'manual' : 'equal',
);

watch(
    () => props.expense,
    (expense) => {
        editForm.clearErrors();

        if (!expense) {
            return;
        }

        editForm.description = expense.description;
        editForm.amount = expense.amount.toFixed(2);

        if (expense.payer?.id) {
            editForm.payerKey = `user:${expense.payer.id}`;
        } else if (expense.payer_email) {
            editForm.payerKey = `email:${expense.payer_email}`;
        } else {
            editForm.payerKey = null;
        }

        if (expense.splits.length > 0) {
            editForm.splits = expense.splits.map((split) => {
                const user = split.user;
                const userId = user?.id ?? split.user_id ?? null;

                if (userId !== null) {
                    return {
                        memberKey: `user:${userId}`,
                        amount: split.amount.toFixed(2),
                    };
                }

                if (split.member_email) {
                    return {
                        memberKey: `email:${split.member_email}`,
                        amount: split.amount.toFixed(2),
                    };
                }

                return {
                    memberKey: null,
                    amount: split.amount.toFixed(2),
                };
            });
        } else {
            editForm.splits = [];
        }
    },
    { immediate: true },
);

function submitEdit() {
    if (!props.expense) {
        return;
    }

    const payload =
        splitMode.value === 'manual'
            ? {
                  ...editForm.data(),
                  payer_key: editForm.payerKey,
                  splits: editForm.splits
                      .filter(
                          (split) =>
                              split.memberKey && split.amount !== '',
                      )
                      .map((split) => {
                          const [kind, value] = (split.memberKey ?? '').split(
                              ':',
                              2,
                          );

                          if (kind === 'user') {
                              return {
                                  user_id: value,
                                  member_email: null,
                                  amount: parseFloat(split.amount),
                              };
                          }

                          if (kind === 'email') {
                              return {
                                  user_id: null,
                                  member_email: value,
                                  amount: parseFloat(split.amount),
                              };
                          }

                          return {
                              user_id: null,
                              member_email: null,
                              amount: parseFloat(split.amount),
                          };
                      }),
              }
            : {
                  ...editForm.data(),
                  payer_key: editForm.payerKey,
                  splits: [],
              };

    editForm.put(`/expenses/${props.expense.id}`, {
        preserveScroll: true,
        data: payload,
        onSuccess: () => {
            isOpen.value = false;
        },
    });
}
</script>

<template>
    <Dialog :open="isOpen" @update:open="isOpen = $event">
        <DialogContent class="sm:max-w-md">
            <DialogHeader>
                <DialogTitle>Edit expense</DialogTitle>
                <DialogDescription>
                    Update the description, payer, or how this expense is split
                    between people.
                </DialogDescription>
            </DialogHeader>

            <form class="space-y-4" @submit.prevent="submitEdit">
                <div class="grid gap-2">
                    <label
                        for="edit-description"
                        class="text-xs font-medium text-foreground"
                    >
                        Description
                    </label>
                    <Input
                        id="edit-description"
                        v-model="editForm.description"
                        type="text"
                        name="description"
                    />
                    <InputError :message="editForm.errors.description" />
                </div>

                <div class="grid gap-2">
                    <label
                        for="edit-amount"
                        class="text-xs font-medium text-foreground"
                    >
                        Total amount
                    </label>
                    <Input
                        id="edit-amount"
                        v-model="editForm.amount"
                        type="number"
                        name="amount"
                        step="0.01"
                        min="0"
                    />
                    <InputError :message="editForm.errors.amount" />
                </div>

                <div class="grid gap-2">
                    <label
                        for="edit-payer"
                        class="text-xs font-medium text-foreground"
                    >
                        Who paid?
                    </label>
                    <select
                        id="edit-payer"
                        v-model="editForm.payerKey"
                        class="rounded-md border border-input bg-background px-2 py-1 text-xs shadow-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                    >
                        <option :value="null" disabled>
                            Select payer
                        </option>
                        <option
                            v-for="option in memberOptions"
                            :key="option.key"
                            :value="option.key"
                        >
                            {{ option.label }}
                        </option>
                    </select>
                    <InputError :message="editForm.errors.payer_key" />
                </div>

                <div
                    v-if="splitMode === 'manual'"
                    class="space-y-3 rounded-md bg-muted/40 p-3"
                >
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <p class="text-xs font-medium text-foreground">
                            Manual splits
                        </p>
                    </div>

                    <div class="space-y-2">
                        <div
                            v-for="(split, index) in editForm.splits"
                            :key="index"
                            class="grid grid-cols-[minmax(0,1fr)_5rem] items-center gap-2 sm:grid-cols-[minmax(0,1fr)_5.5rem]"
                        >
                            <select
                                v-model="split.memberKey"
                                class="h-9 w-full min-w-0 rounded-md border border-input bg-background px-3 py-1 text-xs shadow-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                            >
                                <option :value="null" disabled>
                                    Select member
                                </option>
                                <option
                                    v-for="option in memberOptions"
                                    :key="option.key"
                                    :value="option.key"
                                >
                                    {{ option.label }}
                                </option>
                            </select>
                            <Input
                                v-model="split.amount"
                                type="number"
                                step="0.01"
                                min="0"
                                class="h-9 w-full"
                                placeholder="0.00"
                            />
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center justify-between gap-2 text-xs">
                        <span class="text-muted-foreground">
                            Manual total:
                            <span
                                :class="{
                                    'text-emerald-600 dark:text-emerald-400':
                                        parseFloat(editForm.amount || '0') ===
                                        manualTotal,
                                    'text-amber-600 dark:text-amber-400':
                                        parseFloat(editForm.amount || '0') !==
                                        manualTotal,
                                }"
                            >
                                {{ manualTotal.toFixed(2) }}
                            </span>
                        </span>
                        <span class="text-muted-foreground">
                            Target:
                            {{
                                parseFloat(editForm.amount || '0').toFixed(2)
                            }}
                        </span>
                    </div>

                    <InputError :message="editForm.errors.splits" />
                </div>

                <div class="flex items-center gap-3">
                    <Button
                        type="submit"
                        size="sm"
                        :disabled="editForm.processing"
                    >
                        {{ editForm.processing ? 'Saving...' : 'Save changes' }}
                    </Button>
                </div>
            </form>
        </DialogContent>
    </Dialog>
</template>

