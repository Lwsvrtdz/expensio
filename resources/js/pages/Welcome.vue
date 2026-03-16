<script setup lang="ts">
import { computed } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import { dashboard, login, register } from '@/routes';
import HeroSection from '@/components/marketing/HeroSection.vue';
import HeroPreviewCard from '@/components/marketing/HeroPreviewCard.vue';
import FeaturesSection from '@/components/marketing/FeaturesSection.vue';
import TestimonialsSection from '@/components/marketing/TestimonialsSection.vue';
import TrustedBySection from '@/components/marketing/TrustedBySection.vue';

withDefaults(
    defineProps<{
        canRegister: boolean;
    }>(),
    {
        canRegister: true,
    },
);

const page = usePage();

const isAuthenticated = computed(() => !!page.props.auth?.user);
const dashboardUrl = dashboard();
const loginUrl = login();
const registerUrl = register();
</script>

<template>
    <Head title="Expensio - Manage Expenses, Simplify Sharing">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>

    <div
        class="min-h-screen bg-gradient-to-b from-[#f4f7ff] via-[#fdfdfd] to-[#f7fbff] text-slate-900 antialiased transition-colors duration-500 dark:from-[#020617] dark:via-[#020617] dark:to-[#020617] dark:text-slate-100"
    >
        <!-- Top navigation -->
        <header
            class="sticky top-0 z-30 border-b border-white/60 bg-white/80 backdrop-blur-xl transition-all duration-500 dark:border-slate-800/80 dark:bg-slate-900/80"
        >
            <div
                class="mx-auto flex max-w-6xl items-center justify-between px-6 py-4 lg:px-8"
            >
                <!-- Brand -->
                <div class="flex items-center gap-2">
                    <div
                        class="flex h-9 w-9 items-center justify-center rounded-2xl bg-gradient-to-tr from-sky-500 via-emerald-400 to-cyan-500 shadow-[0_10px_30px_rgba(14,165,233,0.45)] transition-transform duration-500 starting:translate-y-3 starting:opacity-0 starting:blur-sm"
                    >
                        <span
                            class="text-lg font-black text-white drop-shadow-sm"
                        >
                            E
                        </span>
                    </div>
                    <div
                        class="flex flex-col leading-tight opacity-0 transition-all duration-500 delay-100 starting:translate-y-2 starting:opacity-0"
                    >
                        <span class="text-sm font-semibold tracking-tight">
                            Expensio
                        </span>
                        <span
                            class="text-[11px] font-medium uppercase tracking-[0.18em] text-sky-500/80 dark:text-sky-400"
                        >
                            Split smarter
                        </span>
                    </div>
                </div>

                <!-- Nav links -->
                <nav
                    class="hidden items-center gap-8 text-sm font-medium text-slate-600 md:flex dark:text-slate-300"
                >
                    <a href="#features" class="transition-colors hover:text-slate-900 dark:hover:text-white">
                        Features
                    </a>
                    <a href="#how-it-works" class="transition-colors hover:text-slate-900 dark:hover:text-white">
                        How It Works
                    </a>
                    <a href="#pricing" class="transition-colors hover:text-slate-900 dark:hover:text-white">
                        Pricing
                    </a>
                    <a href="#blog" class="transition-colors hover:text-slate-900 dark:hover:text-white">
                        Blog
                    </a>
                </nav>

                <!-- Auth actions -->
                <div class="flex items-center gap-3 text-sm font-medium">
                    <Link
                        v-if="isAuthenticated"
                        :href="dashboardUrl"
                        class="rounded-full border border-slate-200 bg-white/70 px-4 py-1.5 text-slate-800 shadow-sm transition-all hover:border-sky-400 hover:text-sky-700 hover:shadow-md dark:border-slate-700 dark:bg-slate-900/80 dark:text-slate-100 dark:hover:border-sky-500 dark:hover:text-sky-300"
                    >
                        Dashboard
                    </Link>
                    <template v-else>
                        <Link
                            :href="loginUrl"
                            class="rounded-full px-4 py-1.5 text-slate-700 transition-colors hover:text-sky-700 dark:text-slate-200 dark:hover:text-sky-300"
                        >
                            Log in
                        </Link>
                        <Link
                            v-if="canRegister"
                            :href="registerUrl"
                            class="inline-flex items-center gap-1.5 rounded-full bg-gradient-to-r from-sky-500 via-emerald-400 to-cyan-500 px-4 py-1.5 text-sm font-semibold text-white shadow-[0_12px_30px_rgba(56,189,248,0.55)] transition-transform duration-200 hover:-translate-y-0.5 hover:shadow-[0_18px_40px_rgba(56,189,248,0.65)]"
                        >
                            <span>Sign up</span>
                        </Link>
                    </template>
                </div>
            </div>
        </header>

        <!-- Hero -->
        <main
            class="mx-auto flex max-w-6xl flex-col items-center px-6 pb-16 pt-10 lg:flex-row lg:items-stretch lg:gap-10 lg:px-8 lg:pt-16"
        >
            <HeroSection
                :is-authenticated="isAuthenticated"
                :can-register="canRegister"
                :dashboard-url="dashboardUrl"
                :login-url="loginUrl"
                :register-url="registerUrl"
            />
            <HeroPreviewCard />
        </main>

        <FeaturesSection />
        <TestimonialsSection />
        <TrustedBySection />
    </div>
</template>