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

const { balances, authUserKey } = defineProps<{
    balances: Balance[];
    authUserKey: string | null;
}>();
</script>

<template>
    <section
        class="space-y-3 rounded-2xl border border-emerald-500/30 bg-gradient-to-br from-emerald-950/40 via-background to-sky-950/30 p-4 text-sm shadow-sm dark:border-emerald-500/40"
    >
        <div class="flex items-center justify-between gap-3">
            <div class="flex items-center gap-2">
                <div
                    class="flex h-7 w-7 items-center justify-center rounded-full bg-emerald-500/15 text-[11px] font-semibold text-emerald-300"
                >
                    ₿
                </div>
                <div>
                    <h2 class="text-sm font-semibold tracking-tight">
                        Who owes who
                    </h2>
                    <p class="mt-0.5 text-[11px] text-emerald-100/70">
                        Quick overview of who should settle up.
                    </p>
                </div>
            </div>
        </div>

        <div
            v-if="balances.length === 0"
            class="rounded-md border border-dashed border-emerald-500/40 bg-emerald-950/40 p-3 text-xs text-emerald-50"
        >
            Everyone is settled up in this group. 🎉
        </div>

        <ul v-else class="space-y-2">
            <li
                v-for="(balance, index) in balances"
                :key="index"
                class="flex items-center justify-between rounded-xl border px-3 py-2 text-xs transition hover:bg-background/40"
                :class="[
                    authUserKey &&
                    (balance.from.key === authUserKey ||
                        balance.to.key === authUserKey)
                        ? 'border-amber-400/70 bg-amber-900/30'
                        : 'border-sidebar-border/70 bg-background/40 dark:border-sidebar-border',
                ]"
            >
                <span class="flex items-center gap-1.5">
                    <span class="font-medium">
                        {{ balance.from.label }}
                    </span>
                    <span class="mx-1">owe</span>
                    <span class="font-medium">
                        {{ balance.to.label }}
                    </span>
                </span>
                <span
                    class="inline-flex min-w-[4.5rem] justify-end rounded-full bg-emerald-500/15 px-3 py-1 text-[11px] font-semibold text-emerald-100"
                >
                    {{ balance.amount.toFixed(2) }}
                </span>
            </li>
        </ul>
    </section>
</template>

