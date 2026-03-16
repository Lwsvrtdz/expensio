<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { dashboard } from '@/routes';
import groupsRoutes from '@/routes/groups';
import type { BreadcrumbItem } from '@/types';
import { usePage } from '@inertiajs/vue3';

type Group = {
    id: string;
    name: string;
    trip_date: string;
    latest_expense_count?: number;
};

type Summary = {
    total_owed: number;
    total_owes: number;
};

type PageProps = {
    summary: Summary;
    groups: Group[];
};

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
    },
];

const page = usePage<PageProps>();
const summary = computed(() => page.props.summary);
const groups = computed(() => page.props.groups);
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <!-- Summary cards -->
            <div class="grid gap-4 md:grid-cols-2">
                <section
                    class="rounded-xl border border-sidebar-border/70 bg-background/60 p-4 shadow-sm dark:border-sidebar-border"
                >
                    <Heading
                        variant="small"
                        title="You are owed"
                        description="Total others should pay you across all groups."
                    />
                    <p class="mt-4 text-3xl font-semibold tracking-tight">
                        {{ summary.total_owed.toFixed(2) }}
                    </p>
                    <p class="mt-1 text-xs text-muted-foreground">
                        This is the sum of all settled balances where other
                        members owe you money.
                    </p>
                </section>

                <section
                    class="rounded-xl border border-sidebar-border/70 bg-background/60 p-4 shadow-sm dark:border-sidebar-border"
                >
                    <Heading
                        variant="small"
                        title="You owe"
                        description="Total you should pay others across all groups."
                    />
                    <p class="mt-4 text-3xl font-semibold tracking-tight">
                        {{ summary.total_owes.toFixed(2) }}
                    </p>
                    <p class="mt-1 text-xs text-muted-foreground">
                        This is the sum of all settled balances where you owe
                        money to others.
                    </p>
                </section>
            </div>

            <!-- Groups list -->
            <section
                class="flex flex-col gap-4 rounded-xl border border-sidebar-border/70 bg-background/60 p-4 shadow-sm dark:border-sidebar-border"
            >
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <h2 class="text-sm font-semibold tracking-tight">
                            Your groups
                        </h2>
                        <p class="mt-1 text-xs text-muted-foreground">
                            Jump back into groups where you track shared
                            expenses.
                        </p>
                    </div>

                    <Button
                        as-child
                        size="sm"
                    >
                        <Link :href="groupsRoutes.create().url">
                            New group
                        </Link>
                    </Button>
                </div>

                <div
                    v-if="groups.length === 0"
                    class="rounded-lg border border-dashed border-sidebar-border/70 p-6 text-sm text-muted-foreground dark:border-sidebar-border"
                >
                    You haven’t created any groups yet.
                    <Link
                        :href="groupsRoutes.create().url"
                        class="font-medium text-primary underline-offset-4 hover:underline"
                    >
                        Create your first group
                    </Link>
                    to start tracking shared expenses with friends or family.
                </div>

                <div
                    v-else
                    class="grid gap-3 md:grid-cols-2 lg:grid-cols-3"
                >
                    <article
                        v-for="group in groups"
                        :key="group.id"
                        class="flex flex-col justify-between rounded-lg border border-sidebar-border/70 bg-background/40 p-4 text-sm shadow-sm transition hover:border-primary/60 dark:border-sidebar-border"
                    >
                        <div class="space-y-1.5">
                            <h3 class="text-sm font-semibold leading-tight">
                                {{ group.name }}
                            </h3>
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

                        <div class="mt-3 flex items-center justify-between">
                            <Link
                                :href="groupsRoutes.show(group).url"
                                class="text-xs font-medium text-primary underline-offset-4 hover:underline"
                            >
                                View group
                            </Link>
                        </div>
                    </article>
                </div>
            </section>
        </div>
    </AppLayout>
</template>
