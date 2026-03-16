<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import groupsRoutes from '@/routes/groups';
import type { BreadcrumbItem } from '@/types';
import { dashboard } from '@/routes';

const form = useForm({
    name: '',
    trip_date: '',
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
    },
    {
        title: 'Groups',
        href: groupsRoutes.index(),
    },
    {
        title: 'Create group',
        href: groupsRoutes.create(),
    },
];

function submit() {
    form.post(groupsRoutes.store().url, {
        onSuccess: () => form.reset('name', 'trip_date'),
    });
}
</script>

<template>
    <Head title="Create group" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <section class="flex flex-col gap-6 p-4">
            <Heading
                variant="small"
                title="Create a new group"
                description="Set up a group or trip to start tracking shared expenses."
            />

            <form
                @submit.prevent="submit"
                class="max-w-xl space-y-6 rounded-lg border border-sidebar-border/70 bg-background/60 p-6 shadow-sm dark:border-sidebar-border"
            >
                <div class="grid gap-2">
                    <Label for="name">Group name</Label>
                    <Input
                        id="name"
                        v-model="form.name"
                        name="name"
                        type="text"
                        placeholder="Summer trip to Bali"
                        autocomplete="off"
                    />
                    <InputError :message="form.errors.name" />
                </div>

                <div class="grid gap-2">
                    <Label for="trip_date">Trip date</Label>
                    <Input
                        id="trip_date"
                        v-model="form.trip_date"
                        name="trip_date"
                        type="date"
                    />
                    <InputError :message="form.errors.trip_date" />
                </div>

                <div class="flex items-center gap-3">
                    <Button type="submit" :disabled="form.processing">
                        {{ form.processing ? 'Creating...' : 'Create group' }}
                    </Button>

                    <p
                        v-if="form.recentlySuccessful"
                        class="text-sm text-muted-foreground"
                    >
                        Group created.
                    </p>
                </div>
            </form>
        </section>
    </AppLayout>
</template>

