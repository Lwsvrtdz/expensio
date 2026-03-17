<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Users } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { dashboard } from '@/routes';
import groupsRoutes from '@/routes/groups';
import type { BreadcrumbItem } from '@/types';

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
        <div
            class="flex h-full flex-1 flex-col gap-5 overflow-x-auto rounded-xl p-4"
        >
            <section
                class="flex flex-col gap-4 rounded-2xl border border-sidebar-border/70 bg-background/80 p-4 shadow-sm dark:border-sidebar-border"
            >
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <h1 class="text-sm font-semibold tracking-tight">
                            Your groups
                        </h1>
                        <p class="mt-1 text-xs text-muted-foreground">
                            Manage the trips and groups you share expenses with.
                        </p>
                    </div>

                    <Button
                        as-child
                        size="sm"
                        class="rounded-full px-4 py-2 text-xs font-medium"
                    >
                        <Link :href="groupsRoutes.create().url">
                            + New group
                        </Link>
                    </Button>
                </div>

                <div
                    v-if="props.groups.length === 0"
                    class="rounded-lg border border-dashed border-sidebar-border/70 p-6 text-xs text-muted-foreground dark:border-sidebar-border"
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
                        v-for="group in props.groups"
                        :key="group.id"
                        class="flex flex-col justify-between rounded-xl border border-sidebar-border/70 bg-background/90 p-4 text-sm shadow-sm transition hover:-translate-y-0.5 hover:border-primary/70 hover:shadow-md dark:border-sidebar-border"
                    >
                        <div class="flex items-start gap-3">
                            <div
                                class="mt-0.5 flex h-9 w-9 items-center justify-center rounded-2xl bg-gradient-to-tr from-sky-500 via-emerald-400 to-cyan-500 text-xs font-semibold text-white shadow-sm"
                            >
                                <Users class="h-4 w-4" />
                            </div>
                            <div class="flex-1 space-y-1.5">
                                <h2
                                    class="text-sm font-semibold leading-tight line-clamp-1"
                                >
                                    {{ group.name }}
                                </h2>
                                <p class="text-[11px] text-muted-foreground">
                                    Trip date:
                                    <span class="font-medium">
                                        {{ group.trip_date }}
                                    </span>
                                </p>
                                <p class="text-[11px] text-muted-foreground">
                                    Expenses:
                                    <span class="font-medium">
                                        {{ group.latest_expense_count ?? 0 }}
                                    </span>
                                </p>
                            </div>
                        </div>

                        <div
                            class="mt-3 flex items-center justify-between text-[11px]"
                        >
                            <div class="text-muted-foreground">
                                Tap to open group details
                            </div>
                            <Link
                                :href="groupsRoutes.show(group).url"
                                class="inline-flex items-center gap-1 rounded-full bg-primary/5 px-3 py-1 text-[11px] font-medium text-primary hover:bg-primary/10"
                            >
                                View group
                                <span aria-hidden="true">›</span>
                            </Link>
                        </div>
                    </article>
                </div>
            </section>
        </div>
    </AppLayout>
</template>