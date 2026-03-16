<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { home } from '@/routes';

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div
        class="min-h-svh bg-gradient-to-b from-[#f4f7ff] via-[#fdfdfd] to-[#f7fbff] p-6 text-slate-900 antialiased transition-colors duration-500 md:p-10 dark:from-[#020617] dark:via-[#020617] dark:to-[#020617] dark:text-slate-100"
    >
        <div
            class="mx-auto flex w-full max-w-5xl flex-col items-stretch gap-8 lg:flex-row lg:items-center lg:gap-10"
        >
            <!-- Left: brand + context -->
            <div
                class="hidden flex-1 flex-col justify-center gap-6 rounded-3xl border border-white/60 bg-white/70 p-8 shadow-[0_18px_60px_rgba(148,163,184,0.35)] backdrop-blur-xl lg:flex dark:border-slate-800/80 dark:bg-slate-900/80 dark:shadow-[0_18px_60px_rgba(15,23,42,0.85)]"
            >
                <div class="flex items-center gap-3">
                    <div
                        class="flex h-9 w-9 items-center justify-center rounded-2xl bg-gradient-to-tr from-sky-500 via-emerald-400 to-cyan-500 shadow-[0_10px_30px_rgba(14,165,233,0.45)]"
                    >
                        <span class="text-lg font-black text-white drop-shadow-sm">
                            E
                        </span>
                    </div>
                    <div class="leading-tight">
                        <p class="text-sm font-semibold tracking-tight">
                            Expensio
                        </p>
                        <p
                            class="text-[11px] font-medium uppercase tracking-[0.18em] text-sky-600/80 dark:text-sky-300"
                        >
                            Split smarter
                        </p>
                    </div>
                </div>

                <div class="space-y-3">
                    <h1 class="text-2xl font-semibold leading-snug">
                        {{ title || 'Welcome back' }}
                    </h1>
                    <p class="max-w-md text-sm leading-relaxed text-slate-600 dark:text-slate-300">
                        {{ description || 'Track spending, split bills, and stay perfectly in sync with everyone you share expenses with.' }}
                    </p>
                </div>

                <!-- Tiny activity preview -->
                <div
                    class="mt-2 w-full max-w-sm rounded-2xl bg-gradient-to-r from-sky-50 via-white to-emerald-50 p-3 text-[11px] shadow-sm ring-1 ring-sky-100/70 dark:from-slate-900 dark:via-slate-950 dark:to-slate-900 dark:ring-slate-800"
                >
                    <p class="mb-2 text-[10px] font-semibold uppercase tracking-[0.16em] text-slate-500 dark:text-slate-400">
                        Live group snapshot
                    </p>
                    <div class="flex items-center justify-between gap-4">
                        <div class="space-y-1">
                            <p class="text-[11px] font-medium text-slate-800 dark:text-slate-100">
                                Beach Trip · You owe
                            </p>
                            <p class="text-[10px] text-slate-500 dark:text-slate-400">
                                Alex paid for dinner · Lisa settled her share
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-semibold text-emerald-500 dark:text-emerald-300">
                                $120.00
                            </p>
                            <p class="text-[10px] text-slate-400 dark:text-slate-500">
                                Settle in 2 days
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: auth card -->
            <div
                class="flex-1"
            >
                <div
                    class="mx-auto w-full max-w-sm rounded-3xl border border-white/70 bg-white/80 p-6 shadow-[0_18px_60px_rgba(148,163,184,0.45)] backdrop-blur-xl lg:p-7 dark:border-slate-800/80 dark:bg-slate-900/90 dark:shadow-[0_18px_60px_rgba(15,23,42,0.9)]"
                >
                    <div class="mb-6 flex flex-col items-center gap-4 lg:hidden">
                        <Link
                            :href="home()"
                            class="flex flex-col items-center gap-2 font-medium"
                        >
                            <div
                                class="flex h-9 w-9 items-center justify-center rounded-2xl bg-gradient-to-tr from-sky-500 via-emerald-400 to-cyan-500 shadow-[0_10px_30px_rgba(14,165,233,0.45)]"
                            >
                                <AppLogoIcon
                                    class="size-7 fill-current text-white"
                                />
                            </div>
                            <span class="sr-only">{{ title }}</span>
                        </Link>
                        <div class="space-y-2 text-center">
                            <h1 class="text-xl font-medium">
                                {{ title }}
                            </h1>
                            <p class="text-center text-sm text-muted-foreground">
                                {{ description }}
                            </p>
                        </div>
                    </div>

                    <slot />
                </div>
            </div>
        </div>
    </div>
</template>
