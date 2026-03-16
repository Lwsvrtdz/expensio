<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import groupsRoutes from '@/routes/groups';
import { dashboard } from '@/routes';

type Group = {
    id: string;
    name: string;
    trip_date: string;
    latest_expense_count?: number;
};

const props = defineProps<{
    groups: Group[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
    },
    {
        title: 'Groups',
        href: groupsRoutes.index(),
    },
];
</script>

<template>
    <Head title="Groups" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <section class="flex flex-col gap-6 p-4">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h1 class="text-lg font-semibold tracking-tight">
                        Your groups
                    </h1>
                    <p class="mt-1 text-sm text-muted-foreground">
                        Manage the trips and groups you share expenses with.
                    </p>
                </div>

                <Link
                    :href="groupsRoutes.create().url"
                    class="inline-flex items-center rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground shadow-sm transition hover:bg-primary/90"
                >
                    New group
                </Link>
            </div>

            <div
                v-if="props.groups.length === 0"
                class="rounded-lg border border-dashed border-sidebar-border/70 p-6 text-sm text-muted-foreground dark:border-sidebar-border"
            >
                You have no groups yet. Create one to start tracking shared
                expenses.
            </div>

            <div
                v-else
                class="grid gap-4 md:grid-cols-2 lg:grid-cols-3"
            >
                <article
                    v-for="group in props.groups"
                    :key="group.id"
                    class="flex flex-col justify-between rounded-lg border border-sidebar-border/70 bg-background/60 p-4 shadow-sm transition hover:border-primary/60 dark:border-sidebar-border"
                >
                    <div class="space-y-1.5">
                        <h2 class="text-base font-semibold leading-tight">
                            {{ group.name }}
                        </h2>
                        <p class="text-xs text-muted-foreground">
                            Trip date:
                            <span class="font-medium">
                                {{ group.trip_date }}
                            </span>
                        </p>
                        <p class="text-xs text-muted-foreground">
                            Expenses:
                            <span class="font-medium">
                                {{ group.latest_expense_count ?? 0 }}
                            </span>
                        </p>
                    </div>

                    <div class="mt-4 flex items-center justify-between text-sm">
                        <Link
                            :href="groupsRoutes.show(group).url"
                            class="text-primary underline-offset-4 transition hover:underline"
                        >
                            View group
                        </Link>
                    </div>
                </article>
            </div>
        </section>
    </AppLayout>
</template>

