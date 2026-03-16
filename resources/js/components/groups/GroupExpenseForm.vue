<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';

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

const props = defineProps<{
    groupId: string | number;
    members: Member[];
}>();

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

function optionsForRow(currentIndex: number) {
    const selectedKeys = new Set(
        expenseForm.splits
            .map((split, index) =>
                index === currentIndex ? null : split.memberKey,
            )
            .filter((key): key is string => !!key),
    );

    return memberOptions.value.filter((option) => !selectedKeys.has(option.key));
}

const expenseForm = useForm<{
    description: string;
    amount: string;
    splits: {
        memberKey: string | null;
        amount: string;
    }[];
}>({
    description: '',
    amount: '',
    splits: [],
});

const splitMode = ref<'equal' | 'manual'>('equal');

const manualTotal = computed(() =>
    expenseForm.splits.reduce(
        (total, split) => total + (parseFloat(split.amount || '0') || 0),
        0,
    ),
);

function setSplitMode(mode: 'equal' | 'manual') {
    splitMode.value = mode;

    if (mode === 'manual' && expenseForm.splits.length === 0) {
        expenseForm.splits = props.members
            .map((member) => {
                const user = member.user;
                const email = user?.email ?? member.invite_email ?? null;

                if (user) {
                    return {
                        memberKey: `user:${user.id}`,
                        amount: '',
                    };
                }

                if (email) {
                    return {
                        memberKey: `email:${email}`,
                        amount: '',
                    };
                }

                return {
                    memberKey: null,
                    amount: '',
                };
            });
    }
}

function addManualSplitRow() {
    expenseForm.splits.push({
        memberKey: null,
        amount: '',
    });
}

function removeManualSplitRow(index: number) {
    expenseForm.splits.splice(index, 1);
}

function submitExpense() {
    const payload =
        splitMode.value === 'manual'
            ? {
                  ...expenseForm.data(),
                  splits: expenseForm.splits
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
                  ...expenseForm.data(),
                  splits: [],
              };

    expenseForm.post(`/groups/${props.groupId}/expenses`, {
        preserveScroll: true,
        data: payload,
        onSuccess: () => {
            expenseForm.reset('description', 'amount', 'splits');
            splitMode.value = 'equal';
        },
    });
}
</script>

<template>
    <div class="space-y-3">
        <h2 class="text-sm font-semibold tracking-tight">Add expense</h2>

        <form
            class="space-y-3 rounded-md border border-dashed border-sidebar-border/70 p-3 dark:border-sidebar-border"
            @submit.prevent="submitExpense"
        >
            <div class="grid gap-2">
                <label
                    for="description"
                    class="text-xs font-medium text-foreground"
                >
                    Description
                </label>
                <Input
                    id="description"
                    v-model="expenseForm.description"
                    type="text"
                    name="description"
                    placeholder="Dinner, taxi, museum tickets..."
                />
                <InputError :message="expenseForm.errors.description" />
            </div>

            <div class="grid gap-2">
                <label
                    for="amount"
                    class="text-xs font-medium text-foreground"
                >
                    Total amount
                </label>
                <Input
                    id="amount"
                    v-model="expenseForm.amount"
                    type="number"
                    name="amount"
                    step="0.01"
                    min="0"
                />
                <InputError :message="expenseForm.errors.amount" />
            </div>

            <div class="space-y-2">
                <p class="text-xs font-medium text-foreground">Split mode</p>
                <div class="flex gap-2">
                    <Button
                        type="button"
                        size="xs"
                        :variant="splitMode === 'equal' ? 'default' : 'outline'"
                        @click="setSplitMode('equal')"
                    >
                        Split equally
                    </Button>
                    <Button
                        type="button"
                        size="xs"
                        :variant="
                            splitMode === 'manual' ? 'default' : 'outline'
                        "
                        @click="setSplitMode('manual')"
                    >
                        Enter shares manually
                    </Button>
                </div>
                <p class="text-xs text-muted-foreground">
                    Equal split lets the app divide the bill between members
                    automatically. Manual split lets you enter each share.
                </p>
            </div>

            <div
                v-if="splitMode === 'manual'"
                class="space-y-3 rounded-md bg-muted/40 p-3"
            >
                <div class="flex items-center justify-between">
                    <p class="text-xs font-medium text-foreground">
                        Manual splits
                    </p>
                    <Button
                        type="button"
                        size="xs"
                        variant="outline"
                        @click="addManualSplitRow"
                    >
                        Add person
                    </Button>
                </div>

                <div class="space-y-2">
                    <div
                        v-for="(split, index) in expenseForm.splits"
                        :key="index"
                        class="flex items-center gap-2"
                    >
                        <select
                            v-model="split.memberKey"
                            class="flex-1 rounded-md border border-input bg-background px-2 py-1 text-xs shadow-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                        >
                            <option :value="null" disabled>
                                Select member
                            </option>
                            <option
                                v-for="option in optionsForRow(index)"
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
                            class="w-24"
                            placeholder="0.00"
                        />
                        <Button
                            type="button"
                            size="icon"
                            variant="ghost"
                            class="h-8 w-8"
                            @click="removeManualSplitRow(index)"
                        >
                            ×
                        </Button>
                    </div>
                </div>

                <div class="flex items-center justify-between text-xs">
                    <span class="text-muted-foreground">
                        Manual total:
                        <span
                            :class="{
                                'text-emerald-600 dark:text-emerald-400':
                                    parseFloat(expenseForm.amount || '0') ===
                                    manualTotal,
                                'text-amber-600 dark:text-amber-400':
                                    parseFloat(expenseForm.amount || '0') !==
                                    manualTotal,
                            }"
                        >
                            {{ manualTotal.toFixed(2) }}
                        </span>
                    </span>
                    <span class="text-muted-foreground">
                        Target:
                        {{
                            parseFloat(expenseForm.amount || '0').toFixed(2)
                        }}
                    </span>
                </div>

                <InputError :message="expenseForm.errors.splits" />
            </div>

            <div class="flex items-center gap-3">
                <Button
                    type="submit"
                    size="sm"
                    :disabled="expenseForm.processing"
                >
                    {{ expenseForm.processing ? 'Saving...' : 'Add expense' }}
                </Button>
            </div>
        </form>
    </div>
</template>

