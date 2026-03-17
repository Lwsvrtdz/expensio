<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

type Props = {
    isAuthenticated: boolean;
    canRegister: boolean;
    dashboardUrl: string;
    loginUrl: string;
    registerUrl: string;
};

const props = defineProps<Props>();

const primaryCtaHref = computed(() =>
    props.isAuthenticated
        ? props.dashboardUrl
        : props.canRegister
          ? props.registerUrl
          : props.loginUrl,
);
</script>

<template>
    <section
        class="relative flex w-full flex-1 flex-col justify-center gap-6 pb-10 text-center lg:pb-0 lg:text-left"
    >
        <div
            class="inline-flex items-center gap-2 self-center rounded-full border border-sky-100/80 bg-white/80 px-3 py-1 text-[11px] font-medium text-sky-700 shadow-sm ring-1 ring-sky-100/80 backdrop-blur starting:-translate-y-2 starting:opacity-0 starting:blur-sm starting:shadow-none lg:self-start dark:border-sky-900/70 dark:bg-slate-900/90 dark:text-sky-300 dark:ring-sky-900/60"
        >
            <span class="inline-flex h-1.5 w-1.5 rounded-full bg-emerald-400" />
            <span>New: Group expense insights in real‑time</span>
        </div>

        <div
            class="space-y-4 starting:translate-y-3 starting:opacity-0 starting:blur-[1px] starting:duration-700"
        >
            <h1
                class="text-balance text-3xl font-semibold leading-tight tracking-tight sm:text-4xl lg:text-5xl"
            >
                Manage expenses,
                <span
                    class="bg-gradient-to-r from-sky-500 via-emerald-400 to-cyan-500 bg-clip-text text-transparent"
                >
                    simplify sharing
                </span>
                with friends.
            </h1>
            <p
                class="mx-auto max-w-xl text-balance text-sm leading-relaxed text-slate-600 sm:text-base lg:mx-0 dark:text-slate-300"
            >
                Track spending, split bills, and settle up in seconds. Expensio
                keeps every group trip, dinner, and roommate expense clear—so you
                can focus on the fun, not the math.
            </p>
        </div>

        <!-- Primary actions -->
        <div
            class="mt-2 flex flex-col items-center gap-3 starting:translate-y-4 starting:opacity-0 starting:blur-[1px] sm:flex-row lg:items-center lg:justify-start"
        >
            <Link
                :href="primaryCtaHref"
                class="inline-flex w-full items-center justify-center gap-2 rounded-full bg-gradient-to-r from-emerald-400 via-sky-500 to-cyan-500 px-6 py-2.5 text-sm font-semibold text-white shadow-[0_16px_40px_rgba(56,189,248,0.55)] transition-all duration-150 hover:-translate-y-0.5 hover:shadow-[0_22px_55px_rgba(56,189,248,0.7)] sm:w-auto"
            >
                <span>Get Started – It’s Free</span>
                <span
                    class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-white/15 text-[11px]"
                >
                    →
                </span>
            </Link>

            <button
                type="button"
                class="group inline-flex w-full items-center justify-center gap-2 rounded-full border border-slate-200/90 bg-white/80 px-5 py-2.5 text-sm font-medium text-slate-800 shadow-sm transition-all hover:border-sky-200 hover:bg-slate-50 hover:text-sky-700 sm:w-auto dark:border-slate-700 dark:bg-slate-900/80 dark:text-slate-100 dark:hover:border-sky-500/60 dark:hover:bg-slate-800 dark:hover:text-sky-300"
            >
                <span
                    class="flex h-6 w-6 items-center justify-center rounded-full border border-slate-200 bg-white text-[11px] shadow-sm transition-transform group-hover:scale-105 dark:border-slate-600 dark:bg-slate-900"
                >
                    ▶
                </span>
                <span>Watch Demo</span>
            </button>
        </div>

        <!-- Social proof -->
        <div
            class="mt-6 flex flex-col items-center gap-3 text-[11px] text-slate-500 starting:translate-y-4 starting:opacity-0 starting:blur-[1px] sm:flex-row lg:items-center lg:justify-start dark:text-slate-400"
        >
            <div class="flex items-center gap-2">
                <div class="flex -space-x-2">
                    <div
                        class="h-7 w-7 rounded-full border border-white bg-gradient-to-tr from-sky-400 to-emerald-400 shadow-sm dark:border-slate-900"
                    />
                    <div
                        class="h-7 w-7 rounded-full border border-white bg-gradient-to-tr from-amber-400 to-rose-400 shadow-sm dark:border-slate-900"
                    />
                    <div
                        class="h-7 w-7 rounded-full border border-white bg-gradient-to-tr from-indigo-400 to-sky-500 shadow-sm dark:border-slate-900"
                    />
                </div>
                <span>
                    <strong class="font-semibold">4,800+</strong>
                    groups settled up
                </span>
            </div>
            <div
                class="hidden h-px flex-1 bg-gradient-to-r from-slate-200 to-transparent sm:block dark:from-slate-700"
            />
            <div class="flex items-center gap-2">
                <span
                    class="text-xs uppercase tracking-[0.16em] text-slate-400 dark:text-slate-500"
                >
                    Trusted by
                </span>
                <span
                    class="text-[11px] font-medium text-slate-500 dark:text-slate-300"
                >
                    teams, travelers & roommates worldwide
                </span>
            </div>
        </div>
    </section>
</template>

