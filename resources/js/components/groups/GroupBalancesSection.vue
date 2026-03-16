<script setup lang="ts">
type BalanceParty = {
    key: string;
    label: string;
    type: string;
};

type Balance = {
    from: BalanceParty;
    to: BalanceParty;
    amount: number;
};

const props = defineProps<{
    balances: Balance[];
    authUserKey: string | null;
}>();
</script>

<template>
    <section
        class="space-y-3 rounded-lg border border-sidebar-border/70 bg-background/60 p-4 text-sm dark:border-sidebar-border"
    >
        <h2 class="text-sm font-semibold tracking-tight">
            Who owes who
        </h2>

        <div
            v-if="balances.length === 0"
            class="rounded-md border border-dashed border-sidebar-border/70 p-3 text-xs text-muted-foreground dark:border-sidebar-border"
        >
            Everyone is settled up in this group. 🎉
        </div>

        <ul v-else class="space-y-2">
            <li
                v-for="(balance, index) in balances"
                :key="index"
                class="flex items-center justify-between rounded-md border px-3 py-2 text-xs"
                :class="[
                    authUserKey &&
                    balance.from.key === authUserKey
                        ? 'border-amber-500/60 bg-amber-50 dark:border-amber-500/40 dark:bg-amber-900/20'
                        : 'border-sidebar-border/70 dark:border-sidebar-border',
                ]"
            >
                <span>
                    <span class="font-medium">
                        {{
                            authUserKey &&
                            balance.from.key === authUserKey
                                ? 'You'
                                : balance.from.label
                        }}
                    </span>
                    <span class="mx-1">owe</span>
                    <span class="font-medium">
                        {{
                            authUserKey &&
                            balance.to.key === authUserKey
                                ? 'you'
                                : balance.to.label
                        }}
                    </span>
                </span>
                <span class="font-semibold">
                    {{ balance.amount.toFixed(2) }}
                </span>
            </li>
        </ul>
    </section>
</template>

