<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import PlaceholderPattern from '@/components/PlaceholderPattern.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { dashboard } from '@/routes';
import type { BreadcrumbItem } from '@/types';
import { computed, ref } from 'vue';
import { usePage } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
    },
];

const page = usePage<{ auth: { user: { id: number | string; name: string } } }>();
const authUser = computed(() => page.props.auth.user);

const personalExpenseForm = useForm<{
    description: string;
    amount: string;
    splits: {
        user_id: number | string | null;
        member_email: string | null;
        amount: string;
    }[];
}>({
    description: '',
    amount: '',
    splits: [],
});

const personalSplitMode = ref<'equal' | 'manual'>('equal');

const personalManualTotal = computed(() =>
    personalExpenseForm.splits.reduce(
        (total, split) => total + (parseFloat(split.amount || '0') || 0),
        0,
    ),
);

function setPersonalSplitMode(mode: 'equal' | 'manual') {
    personalSplitMode.value = mode;

    if (mode === 'manual' && personalExpenseForm.splits.length === 0) {
        personalExpenseForm.splits.push({
            user_id: authUser.value?.id ?? null,
            member_email: null,
            amount: '',
        });
    }
}

function addPersonalSplitRow() {
    personalExpenseForm.splits.push({
        user_id: null,
        member_email: '',
        amount: '',
    });
}

function removePersonalSplitRow(index: number) {
    personalExpenseForm.splits.splice(index, 1);
}

function submitPersonalExpense() {
    const payload =
        personalSplitMode.value === 'manual'
            ? {
                  ...personalExpenseForm.data(),
                  splits: personalExpenseForm.splits
                      .filter(
                          (split) =>
                              (split.user_id || split.member_email) &&
                              split.amount !== '',
                      )
                      .map((split) => ({
                          user_id: split.user_id || null,
                          member_email: split.member_email,
                          amount: parseFloat(split.amount),
                      })),
              }
            : {
                  ...personalExpenseForm.data(),
                  splits: [],
              };

    personalExpenseForm.post('/expenses', {
        preserveScroll: true,
        data: payload,
        onSuccess: () => {
            personalExpenseForm.reset('description', 'amount', 'splits');
            personalSplitMode.value = 'equal';
        },
    });
}
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                <div
                    class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border"
                >
                    <PlaceholderPattern />
                </div>
                <div
                    class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border"
                >
                    <PlaceholderPattern />
                </div>
                <div
                    class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border"
                >
                    <PlaceholderPattern />
                </div>
            </div>
            <div
                class="grid gap-4 rounded-xl border border-sidebar-border/70 bg-background/60 p-4 md:grid-cols-[minmax(0,1.6fr)_minmax(0,1.4fr)] dark:border-sidebar-border"
            >
                <section class="space-y-3">
                    <Heading
                        variant="small"
                        title="Personal expenses"
                        description="Track expenses that aren't part of a group."
                    />

                    <form
                        class="space-y-3 rounded-md border border-dashed border-sidebar-border/70 p-3 text-sm dark:border-sidebar-border"
                        @submit.prevent="submitPersonalExpense"
                    >
                        <div class="grid gap-2">
                            <label
                                for="personal_description"
                                class="text-xs font-medium text-foreground"
                            >
                                Description
                            </label>
                            <Input
                                id="personal_description"
                                v-model="personalExpenseForm.description"
                                type="text"
                                name="description"
                                placeholder="Coffee, subscription, groceries..."
                            />
                            <InputError
                                :message="personalExpenseForm.errors.description"
                            />
                        </div>

                        <div class="grid gap-2">
                            <label
                                for="personal_amount"
                                class="text-xs font-medium text-foreground"
                            >
                                Total amount
                            </label>
                            <Input
                                id="personal_amount"
                                v-model="personalExpenseForm.amount"
                                type="number"
                                name="amount"
                                step="0.01"
                                min="0"
                            />
                            <InputError
                                :message="personalExpenseForm.errors.amount"
                            />
                        </div>

                        <div class="space-y-2">
                            <p class="text-xs font-medium text-foreground">
                                Split mode
                            </p>
                            <div class="flex gap-2">
                                <Button
                                    type="button"
                                    size="xs"
                                    :variant="
                                        personalSplitMode === 'equal'
                                            ? 'default'
                                            : 'outline'
                                    "
                                    @click="setPersonalSplitMode('equal')"
                                >
                                    Just me (equal)
                                </Button>
                                <Button
                                    type="button"
                                    size="xs"
                                    :variant="
                                        personalSplitMode === 'manual'
                                            ? 'default'
                                            : 'outline'
                                    "
                                    @click="setPersonalSplitMode('manual')"
                                >
                                    Manual split
                                </Button>
                            </div>
                            <p class="text-xs text-muted-foreground">
                                Equal split will assign the full amount to you.
                                Manual split lets you share this expense with
                                others by email.
                            </p>
                        </div>

                        <div
                            v-if="personalSplitMode === 'manual'"
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
                                    @click="addPersonalSplitRow"
                                >
                                    Add person
                                </Button>
                            </div>

                            <div class="space-y-2">
                                <div
                                    v-for="(split, index) in personalExpenseForm.splits"
                                    :key="index"
                                    class="flex items-center gap-2"
                                >
                                    <Input
                                        v-model="split.member_email"
                                        type="email"
                                        class="flex-1"
                                        placeholder="friend@example.com"
                                    />
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
                                        @click="removePersonalSplitRow(index)"
                                    >
                                        ×
                                    </Button>
                                </div>
                            </div>

                            <div
                                class="flex items-center justify-between text-xs"
                            >
                                <span class="text-muted-foreground">
                                    Manual total:
                                    <span
                                        :class="{
                                            'text-emerald-600 dark:text-emerald-400':
                                                parseFloat(
                                                    personalExpenseForm.amount ||
                                                        '0',
                                                ) === personalManualTotal,
                                            'text-amber-600 dark:text-amber-400':
                                                parseFloat(
                                                    personalExpenseForm.amount ||
                                                        '0',
                                                ) !== personalManualTotal,
                                        }"
                                    >
                                        {{ personalManualTotal.toFixed(2) }}
                                    </span>
                                </span>
                                <span class="text-muted-foreground">
                                    Target:
                                    {{
                                        parseFloat(
                                            personalExpenseForm.amount || '0',
                                        ).toFixed(2)
                                    }}
                                </span>
                            </div>

                            <InputError
                                :message="personalExpenseForm.errors.splits"
                            />
                        </div>

                        <div class="flex items-center gap-3">
                            <Button
                                type="submit"
                                size="sm"
                                :disabled="personalExpenseForm.processing"
                            >
                                {{
                                    personalExpenseForm.processing
                                        ? 'Saving...'
                                        : 'Add expense'
                                }}
                            </Button>
                        </div>
                    </form>
                </section>

                <section
                    class="hidden rounded-md border border-dashed border-sidebar-border/70 p-4 text-sm text-muted-foreground md:block dark:border-sidebar-border"
                >
                    <Heading
                        variant="small"
                        title="Overview coming soon"
                        description="A summary of recent expenses and balances will appear here."
                    />
                    <div class="mt-4 space-y-2">
                        <p>
                            For now, you can record personal expenses and group
                            expenses from their respective pages.
                        </p>
                        <p>
                            Use groups when you’re sharing costs with others;
                            use personal expenses for everything else.
                        </p>
                    </div>
                </section>
            </div>
        </div>
    </AppLayout>
</template>
