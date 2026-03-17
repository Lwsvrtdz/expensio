<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { dashboard, home } from '@/routes';
</script>

<template>
    <Head title="Invitation expired" />

    <main
        class="flex min-h-screen items-center justify-center bg-background px-4 py-12"
    >
        <section
            class="w-full max-w-md rounded-xl border border-sidebar-border/70 bg-background/80 p-6 text-sm shadow-sm dark:border-sidebar-border"
        >
            <h1 class="text-lg font-semibold tracking-tight">
                This invitation has expired
            </h1>

            <p class="mt-3 text-muted-foreground">
                The link you followed is no longer valid. Invitation links
                expire after 7 days to keep your account and groups secure.
            </p>

            <p class="mt-3 text-muted-foreground">
                If you still need access to this group, ask the person who
                invited you to send a new invitation.
            </p>

            <div class="mt-6 flex flex-col gap-2">
                <Button as-child class="w-full">
                    <Link :href="dashboard().url">
                        Go to your dashboard
                    </Link>
                </Button>
                <Button as-child variant="outline" class="w-full">
                    <Link :href="home().url">
                        Back to home
                    </Link>
                </Button>
            </div>
        </section>
    </main>
</template>

