<script setup lang="ts">
import GroupExpenseForm from '@/components/groups/GroupExpenseForm.vue';
import GroupExpenseList from '@/components/groups/GroupExpenseList.vue';

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

type Expense = {
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
};

defineProps<{
    groupId: string;
    members: Member[];
    expenses: Expense[];
    authUserId: string | number | null;
    authUserEmail: string | null;
}>();
</script>

<template>
    <section
        class="grid gap-4 rounded-lg border border-sidebar-border/70 bg-background/60 p-4 text-sm dark:border-sidebar-border md:grid-cols-[minmax(0,1.4fr)_minmax(0,2fr)]"
    >
        <GroupExpenseForm
            :group-id="groupId"
            :members="members"
            :auth-user-id="authUserId"
        />

        <GroupExpenseList
            :expenses="expenses"
            :auth-user-id="authUserId"
            :auth-user-email="authUserEmail"
        />
    </section>
</template>

