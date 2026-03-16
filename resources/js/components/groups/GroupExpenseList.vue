<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Trash2 } from 'lucide-vue-next';

type MemberUser = {
    id: string | number;
    name: string;
    email: string;
} | null;

type ExpenseSplit = {
    id: string | number;
    amount: number;
    settled: boolean;
    member_email?: string | null;
    user?: MemberUser;
};

type Expense = {
    id: string | number;
    description: string;
    amount: number;
    paid_by: string | number;
    payer?: MemberUser;
    splits: ExpenseSplit[];
};

const props = defineProps<{
    expenses: Expense[];
    authUserId: string | number | null;
}>();

const deleteForm = useForm({});

function destroyExpense(expenseId: string | number) {
    if (!confirm('Delete this expense?')) {
        return;
    }

    deleteForm.delete(`/expenses/${expenseId}`, {
        preserveScroll: true,
    });
}
</script>

<template>
    <div class="space-y-3">
        <h2 class="text-sm font-semibold tracking-tight">Expenses</h2>

        <div
            v-if="expenses.length === 0"
            class="rounded-md border border-dashed border-sidebar-border/70 p-3 text-xs text-muted-foreground dark:border-sidebar-border"
        >
            No expenses recorded yet. Add one to start tracking who paid for
            what.
        </div>

        <ul v-else class="space-y-3">
            <li
                v-for="expense in expenses"
                :key="expense.id"
                class="rounded-md border border-sidebar-border/70 bg-background/80 p-3 text-xs shadow-sm dark:border-sidebar-border"
            >
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="font-medium">
                            {{ expense.description }}
                        </p>
                        <p class="mt-0.5 text-muted-foreground">
                            Paid by
                            <span class="font-medium">
                                {{
                                    expense.payer?.name ||
                                    expense.payer?.email ||
                                    'Unknown'
                                }}
                            </span>
                            ·
                            <span class="font-medium">
                                {{ expense.amount.toFixed(2) }}
                            </span>
                        </p>
                    </div>

                    <Button
                        v-if="authUserId !== null && authUserId === expense.paid_by"
                        type="button"
                        size="icon-sm"
                        variant="ghost"
                        class="text-destructive hover:bg-destructive/10"
                        @click="destroyExpense(expense.id)"
                    >
                        <Trash2 class="h-3 w-3" />
                        <span class="sr-only">Delete</span>
                    </Button>
                </div>

                <div
                    v-if="expense.splits.length > 0"
                    class="mt-2 space-y-1"
                >
                    <p class="text-[11px] font-medium text-muted-foreground">
                        Splits
                    </p>
                    <ul class="space-y-0.5">
                        <li
                            v-for="split in expense.splits"
                            :key="split.id"
                            class="flex items-center justify-between"
                        >
                            <span class="text-[11px] text-muted-foreground">
                                {{
                                    split.user?.name ||
                                    split.user?.email ||
                                    split.member_email
                                }}
                            </span>
                            <span class="text-[11px] font-medium">
                                {{ split.amount.toFixed(2) }}
                            </span>
                        </li>
                    </ul>
                </div>
            </li>
        </ul>
    </div>
</template>

