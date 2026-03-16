<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { useForm } from '@inertiajs/vue3';
import groupsRoutes from '@/routes/groups';

type Group = {
    id: string;
    name: string;
    trip_date: string;
};

const props = defineProps<{
    group: Group;
    isCreator: boolean;
}>();

const form = useForm({});

function destroyGroup() {
    if (!confirm('Are you sure you want to delete this group?')) {
        return;
    }

    form.delete(groupsRoutes.destroy(props.group).url, {
        preserveScroll: true,
    });
}
</script>

<template>
    <div class="flex items-start justify-between gap-4">
        <div>
            <Heading
                variant="small"
                :title="group.name"
                :description="`Trip date: ${group.trip_date}`"
            />
            <p class="mt-2 text-sm text-muted-foreground">
                Manage members and track shared expenses for this group.
            </p>
        </div>

        <div v-if="isCreator" class="flex items-center gap-2">
            <Button
                variant="outline"
                class="border-destructive text-destructive hover:bg-destructive/10"
                type="button"
                @click="destroyGroup"
            >
                Delete group
            </Button>
        </div>
    </div>
</template>

