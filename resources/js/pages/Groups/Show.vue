<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import GroupBalancesSection from '@/components/groups/GroupBalancesSection.vue';
import GroupExpensesSection from '@/components/groups/GroupExpensesSection.vue';
import GroupHeader from '@/components/groups/GroupHeader.vue';
import GroupMembersSection from '@/components/groups/GroupMembersSection.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import groupsRoutes from '@/routes/groups';
import type { BreadcrumbItem } from '@/types';

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
    expenses: {
        id: string | number;
        description: string;
        amount: number;
        paid_by: string | number;
        payer?: MemberUser;
        splits: {
            id: string | number;
            amount: number;
            settled: boolean;
            member_email?: string | null;
            user?: MemberUser;
        }[];
    }[];
};

type BalanceParty = {
    key: string;
    label: string;
    type: string;
};

type Balance = {
    from: BalanceParty;
    to: BalanceParty;
    amount: number;
};

const props = defineProps<{
    group: Group;
    balances: Balance[];
}>();

const page = usePage<{
    auth: { user: { id: string | number; name?: string; email?: string } };
}>();
const authUser = computed(() => page.props.auth.user);
const authUserKey = computed(() =>
    authUser.value ? `user:${authUser.value.id}` : null,
);

const isCreator = computed(
    () => authUser.value && authUser.value.id === props.group.created_by,
);

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
            <GroupHeader :group="group" :is-creator="isCreator" />

            <div
                class="grid gap-4 md:grid-cols-[minmax(0,1.4fr)_minmax(0,1fr)]"
            >
                <GroupBalancesSection
                    :balances="balances"
                    :auth-user-key="authUserKey"
                />

                <GroupMembersSection
                    :group-id="group.id"
                    :members="group.members"
                />
            </div>

            <GroupExpensesSection
                :group-id="group.id"
                :members="group.members"
                :expenses="group.expenses"
                :auth-user-id="authUser ? authUser.id : null"
                :auth-user-email="authUser?.email ?? null"
            />

            
        </section>
    </AppLayout>
</template>

