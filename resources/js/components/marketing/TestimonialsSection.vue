<script setup lang="ts">
</script>

<template>
    <section
        id="testimonials"
        class="mx-auto max-w-6xl px-6 pb-16 lg:px-8"
    >
        <div
            class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-sky-500 via-emerald-400 to-cyan-500 px-6 py-10 text-white shadow-[0_24px_80px_rgba(15,23,42,0.35)] lg:px-10"
        >
            <div class="absolute inset-y-0 right-0 opacity-20">
                <div
                    class="h-full w-64 bg-[radial-gradient(circle_at_top,_rgba(255,255,255,0.8),_transparent_60%)] blur-3xl"
                />
            </div>

            <div class="relative grid gap-8 lg:grid-cols-[1.2fr,1.1fr]">
                <div class="space-y-4">
                    <p
                        class="text-xs font-semibold uppercase tracking-[0.24em]"
                    >
                        Loved by modern teams
                    </p>
                    <h2
                        class="text-balance text-2xl font-semibold leading-snug sm:text-3xl"
                    >
                        “Expensio turned our chaotic trip receipts into a calm,
                        one‑tap settle up.”
                    </h2>
                    <p class="max-w-xl text-sm text-sky-50/90">
                        From group travel to housemates, Expensio keeps everyone
                        on the same page. Clear balances, transparent history,
                        and zero awkward conversations.
                    </p>
                </div>

                <div class="grid gap-4 text-sm md:grid-cols-2">
                    <figure
                        class="rounded-2xl bg-white/10 p-4 shadow-lg backdrop-blur"
                    >
                        <figcaption class="flex items-center gap-3">
                            <div
                                class="h-9 w-9 rounded-2xl bg-gradient-to-tr from-amber-400 to-rose-500"
                            />
                            <div>
                                <p class="text-sm font-semibold">
                                    Samir • Trip Planner
                                </p>
                                <p class="text-[11px] text-sky-50/80">
                                    Barcelona friend group
                                </p>
                            </div>
                        </figcaption>
                        <p class="mt-3 text-[13px] leading-relaxed text-sky-50">
                            “We used to sync spreadsheets after every dinner.
                            Now everyone just snaps a photo, adds an expense, and
                            Expensio keeps the balances fair automatically.”
                        </p>
                    </figure>

                    <div class="space-y-4">
                        <figure
                            class="rounded-2xl bg-white/10 p-4 shadow-lg backdrop-blur"
                        >
                            <figcaption class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-semibold">
                                        Chloe • Product Manager
                                    </p>
                                    <p class="text-[11px] text-sky-50/80">
                                        Remote team offsites
                                    </p>
                                </div>
                                <span
                                    class="rounded-full bg-emerald-400/20 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-[0.16em] text-emerald-50"
                                >
                                    52 trips
                                </span>
                            </figcaption>
                            <p class="mt-3 text-[13px] leading-relaxed text-sky-50">
                                “Every retro now includes ‘Expensio’ in the
                                ‘what went well’ column. No one worries about
                                chasing receipts anymore.”
                            </p>
                        </figure>

                        <div class="flex items-center justify-between text-xs">
                            <div class="flex items-center gap-2">
                                <span class="text-2xl font-semibold">4.9</span>
                                <span class="text-[11px] text-sky-50/80">
                                    Average rating across travel &amp; finance
                                    communities
                                </span>
                            </div>
                            <div class="hidden items-center gap-1 sm:flex">
                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-300" />
                                <span class="text-[11px] text-sky-50/80">
                                    “Set‑and‑forget” peace of mind
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

