<script setup lang="ts">
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import groupsRoutes from '@/routes/groups';
import membersRoutes from '@/routes/groups/members';
import type { BreadcrumbItem } from '@/types';
import { dashboard } from '@/routes';

type MemberUser = {
    id: string | number;
    name: string;
    email: string;
} | null;

type Member = {
    id: string;
    status: string;
    invite_email?: string | null;
    user?: MemberUser;
};

type Group = {
    id: string;
    name: string;
    trip_date: string;
    created_by: string | number;
    members: Member[];
    expenses: unknown[];
};

const props = defineProps<{
    group: Group;
}>();

const page = usePage<{ auth: { user: { id: string | number } } }>();
const authUser = computed(() => page.props.auth.user);

const isCreator = computed(
    () => authUser.value && authUser.value.id === props.group.created_by,
);

const inviteForm = useForm({
    email: '',
});

function submitInvite() {
    inviteForm.post(membersRoutes.store(props.group).url, {
        onSuccess: () => inviteForm.reset('email'),
        preserveScroll: true,
    });
}

function destroyGroup() {
    if (!confirm('Are you sure you want to delete this group?')) {
        return;
    }

    inviteForm.delete(groupsRoutes.destroy(props.group).url, {
        preserveScroll: true,
    });
}

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
        title: props.group.name,
        href: groupsRoutes.show(props.group),
    },
];
</script>

<template>
    <Head :title="group.name" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <section class="flex flex-col gap-6 p-4">
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

            <!-- Members -->
            <section
                class="grid gap-4 rounded-lg border border-sidebar-border/70 bg-background/60 p-4 shadow-sm dark:border-sidebar-border md:grid-cols-[minmax(0,2fr)_minmax(0,1.5fr)]"
            >
                <div class="space-y-3">
                    <h2 class="text-sm font-semibold tracking-tight">
                        Members
                    </h2>

                    <ul class="divide-y divide-border rounded-md border">
                        <li
                            v-for="member in group.members"
                            :key="member.id"
                            class="flex items-center justify-between gap-3 px-3 py-2 text-sm"
                        >
                            <div class="flex flex-col">
                                <span class="font-medium">
                                    {{
                                        member.user?.name ||
                                        member.user?.email ||
                                        member.invite_email
                                    }}
                                </span>
                                <span
                                    v-if="member.user?.email"
                                    class="text-xs text-muted-foreground"
                                >
                                    {{ member.user.email }}
                                </span>
                            </div>

                            <span
                                class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium"
                                :class="[
                                    member.status === 'accepted'
                                        ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-200'
                                        : 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-200',
                                ]"
                            >
                                {{
                                    member.status === 'accepted'
                                        ? 'Accepted'
                                        : 'Pending'
                                }}
                            </span>
                        </li>
                    </ul>
                </div>

                <!-- Invite form -->
                <div class="space-y-3">
                    <h2 class="text-sm font-semibold tracking-tight">
                        Invite members
                    </h2>

                    <form
                        @submit.prevent="submitInvite"
                        class="space-y-3 rounded-md border border-dashed border-sidebar-border/70 p-3 text-sm dark:border-sidebar-border"
                    >
                        <p class="text-xs text-muted-foreground">
                            Invite someone by email. Existing users are added
                            immediately; others receive an email invitation.
                        </p>

                        <div class="grid gap-2">
                            <label
                                for="invite_email"
                                class="text-xs font-medium text-foreground"
                            >
                                Email address
                            </label>
                            <Input
                                id="invite_email"
                                v-model="inviteForm.email"
                                type="email"
                                name="email"
                                autocomplete="email"
                                placeholder="friend@example.com"
                            />
                            <InputError :message="inviteForm.errors.email" />
                        </div>

                        <div class="flex items-center gap-3">
                            <Button
                                type="submit"
                                size="sm"
                                :disabled="inviteForm.processing"
                            >
                                {{
                                    inviteForm.processing
                                        ? 'Sending...'
                                        : 'Send invite'
                                }}
                            </Button>

                            <p
                                v-if="inviteForm.recentlySuccessful"
                                class="text-xs text-muted-foreground"
                            >
                                Invitation sent.
                            </p>
                        </div>
                    </form>
                </div>
            </section>

            <!-- Expenses placeholder -->
            <section
                class="space-y-3 rounded-lg border border-dashed border-sidebar-border/70 bg-background/40 p-4 text-sm text-muted-foreground dark:border-sidebar-border"
            >
                <div class="flex items-center justify-between">
                    <h2 class="text-sm font-semibold tracking-tight">
                        Expenses
                    </h2>
                    <span class="text-xs uppercase tracking-wide">
                        Coming soon
                    </span>
                </div>

                <p>
                    Expense tracking and settlement will appear here. You’ll be
                    able to add expenses, see who owes what, and record
                    settlements.
                </p>
            </section>
        </section>
    </AppLayout>
</template>

