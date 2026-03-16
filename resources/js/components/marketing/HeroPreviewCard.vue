<script setup lang="ts">
</script>

<template>
    <section
        aria-hidden="true"
        class="relative mt-10 w-full flex-1 lg:mt-0"
    >
        <div
            class="relative mx-auto h-[430px] max-w-xs overflow-visible rounded-[28px] bg-gradient-to-b from-sky-50 via-white to-emerald-50 p-4 shadow-[0_24px_80px_rgba(15,23,42,0.15)] ring-1 ring-sky-100/70 starting:translate-y-6 starting:opacity-0 starting:blur-sm dark:from-slate-900 dark:via-slate-950 dark:to-slate-900 dark:ring-slate-800"
        >
            <!-- Card header -->
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div
                        class="h-8 w-8 rounded-2xl bg-gradient-to-tr from-sky-500 to-emerald-400 text-center text-sm font-semibold text-white shadow-sm"
                    >
                        <span class="leading-8">E</span>
                    </div>
                    <div class="leading-tight">
                        <p
                            class="text-xs font-semibold text-slate-900 dark:text-slate-100"
                        >
                            Group: Beach Trip
                        </p>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">
                            July getaway, Bali
                        </p>
                    </div>
                </div>
                <span
                    class="rounded-full bg-sky-50 px-2 py-1 text-[10px] font-semibold uppercase tracking-[0.14em] text-sky-600 dark:bg-sky-900/40 dark:text-sky-300"
                >
                    LIVE
                </span>
            </div>

            <!-- Summary pills -->
            <div class="mt-4 grid grid-cols-2 gap-3 text-[11px]">
                <div
                    class="rounded-2xl bg-white/90 p-3 shadow-sm ring-1 ring-emerald-100/80 transition hover:-translate-y-0.5 hover:shadow-md dark:bg-slate-900/80 dark:ring-emerald-500/30"
                >
                    <p class="text-[10px] font-medium text-emerald-600 dark:text-emerald-300">
                        You owe
                    </p>
                    <p
                        class="mt-1 text-lg font-semibold text-emerald-500 dark:text-emerald-300"
                    >
                        $120.00
                    </p>
                    <p class="mt-1 text-[10px] text-slate-500 dark:text-slate-400">
                        Settle up by Friday
                    </p>
                </div>
                <div
                    class="rounded-2xl bg-white/90 p-3 shadow-sm ring-1 ring-amber-100/80 transition hover:-translate-y-0.5 hover:shadow-md dark:bg-slate-900/80 dark:ring-amber-500/40"
                >
                    <p class="text-[10px] font-medium text-amber-600 dark:text-amber-300">
                        You are owed
                    </p>
                    <p
                        class="mt-1 text-lg font-semibold text-amber-500 dark:text-amber-300"
                    >
                        $85.50
                    </p>
                    <p class="mt-1 text-[10px] text-slate-500 dark:text-slate-400">
                        3 friends still pending
                    </p>
                </div>
            </div>

            <!-- Recent activity list -->
            <div
                class="mt-4 rounded-2xl bg-white/95 p-3 text-[11px] shadow-sm ring-1 ring-slate-100/80 dark:bg-slate-900/90 dark:ring-slate-800"
            >
                <p
                    class="mb-2 text-[10px] font-semibold uppercase tracking-[0.16em] text-slate-400"
                >
                    Recent Activity
                </p>
                <ul class="space-y-2">
                    <li class="flex items-center justify-between gap-2">
                        <div class="flex items-center gap-2">
                            <span
                                class="flex h-5 w-5 items-center justify-center rounded-full bg-emerald-50 text-[10px] text-emerald-500 dark:bg-emerald-900/40 dark:text-emerald-300"
                            >
                                A
                            </span>
                            <span class="text-slate-600 dark:text-slate-200">
                                Alex paid for dinner
                            </span>
                        </div>
                        <span
                            class="font-semibold text-emerald-500 dark:text-emerald-300"
                        >
                            $75.00
                        </span>
                    </li>
                    <li class="flex items-center justify-between gap-2">
                        <div class="flex items-center gap-2">
                            <span
                                class="flex h-5 w-5 items-center justify-center rounded-full bg-sky-50 text-[10px] text-sky-500 dark:bg-sky-900/40 dark:text-sky-300"
                            >
                                Y
                            </span>
                            <span class="text-slate-600 dark:text-slate-200">
                                You added taxi ride
                            </span>
                        </div>
                        <span class="font-semibold text-slate-700 dark:text-slate-100">
                            $40.00
                        </span>
                    </li>
                    <li class="flex items-center justify-between gap-2">
                        <div class="flex items-center gap-2">
                            <span
                                class="flex h-5 w-5 items-center justify-center rounded-full bg-amber-50 text-[10px] text-amber-500 dark:bg-amber-900/40 dark:text-amber-300"
                            >
                                L
                            </span>
                            <span class="text-slate-600 dark:text-slate-200">
                                Lisa settled her share
                            </span>
                        </div>
                        <span
                            class="font-semibold text-emerald-500 dark:text-emerald-300"
                        >
                            Paid
                        </span>
                    </li>
                </ul>
            </div>

            <!-- Floating monthly spending card -->
            <div
                class="pointer-events-none absolute -right-10 top-24 w-40 origin-top-right rounded-2xl bg-white/95 p-3 text-[10px] shadow-[0_18px_40px_rgba(15,23,42,0.35)] ring-1 ring-slate-100/80 transition-transform duration-[1800ms] ease-[cubic-bezier(0.19,1,0.22,1)] starting:translate-y-6 starting:scale-95 starting:opacity-0 dark:bg-slate-950/95 dark:ring-slate-800"
            >
                <p class="mb-1 text-[10px] font-semibold text-slate-700 dark:text-slate-100">
                    Expense Overview
                </p>
                <p class="text-[10px] text-slate-500 dark:text-slate-400">
                    Monthly spending
                </p>
                <div class="mt-2 flex items-center gap-3">
                    <div
                        class="relative h-14 w-14 rounded-full bg-gradient-to-tr from-sky-500 via-emerald-400 to-amber-400"
                    >
                        <div
                            class="absolute inset-1 rounded-full bg-white dark:bg-slate-950"
                        />
                        <div
                            class="absolute inset-2 rounded-full bg-gradient-to-b from-sky-50 via-white to-emerald-50 text-[11px] font-semibold text-slate-800 dark:from-slate-900 dark:via-slate-950 dark:to-slate-900 dark:text-slate-100"
                        >
                            <div
                                class="flex h-full flex-col items-center justify-center"
                            >
                                <span class="text-[9px] uppercase tracking-[0.16em] text-slate-400">
                                    Total
                                </span>
                                <span>$1,280</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex-1 space-y-1 text-[9px]">
                        <div class="flex items-center justify-between">
                            <span class="flex items-center gap-1">
                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-400" />
                                Groceries
                            </span>
                            <span class="font-semibold">$510</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="flex items-center gap-1">
                                <span class="h-1.5 w-1.5 rounded-full bg-sky-400" />
                                Entertainment
                            </span>
                            <span class="font-semibold">$450</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="flex items-center gap-1">
                                <span class="h-1.5 w-1.5 rounded-full bg-amber-400" />
                                Utilities
                            </span>
                            <span class="font-semibold">$320</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Floating avatar card -->
            <div
                class="pointer-events-none absolute -left-8 bottom-8 flex w-44 items-center gap-3 rounded-2xl bg-white/95 p-3 text-[10px] shadow-[0_16px_40px_rgba(15,23,42,0.35)] ring-1 ring-slate-100/80 transition-transform delay-150 duration-[1900ms] ease-[cubic-bezier(0.19,1,0.22,1)] starting:translate-y-6 starting:scale-95 starting:opacity-0 dark:bg-slate-950/95 dark:ring-slate-800"
            >
                <div
                    class="h-9 w-9 rounded-2xl bg-gradient-to-tr from-sky-500 via-indigo-500 to-purple-500"
                />
                <div class="space-y-0.5">
                    <p
                        class="text-[11px] font-semibold text-slate-800 dark:text-slate-100"
                    >
                        Maya just joined
                    </p>
                    <p class="text-[10px] text-slate-500 dark:text-slate-400">
                        “Beach Trip” · 3 active balances
                    </p>
                </div>
            </div>
        </div>
    </section>
</template>

