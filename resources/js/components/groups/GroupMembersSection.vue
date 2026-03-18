<script setup lang="ts">
import { ref } from 'vue';
import { Button } from '@/components/ui/button';
import GroupInviteMembersModal from '@/components/groups/GroupInviteMembersModal.vue';

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

const props = defineProps<{
    groupId: string;
    members: Member[];
}>();

const inviteOpen = ref(false);
</script>

<template>
    <section
        class="rounded-lg border border-sidebar-border/70 bg-background/60 p-4 shadow-sm dark:border-sidebar-border"
    >
        <div class="mb-4 flex items-center justify-between gap-3">
            <h2 class="text-sm font-semibold tracking-tight">
                Members
            </h2>

            <Button
                type="button"
                size="sm"
                variant="outline"
                class="hidden md:inline-flex items-center gap-1 px-3 py-1 text-xs"
                @click="inviteOpen = true"
            >
                Invite members
            </Button>
        </div>

        <div class="space-y-3">
            <ul class="divide-y divide-border rounded-md border">
                <li
                    v-for="member in members"
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

            <Button
                type="button"
                size="sm"
                class="mt-2 w-full md:hidden"
                variant="outline"
                @click="inviteOpen = true"
            >
                Invite members
            </Button>
        </div>

        <GroupInviteMembersModal
            v-model="inviteOpen"
            :group-id="groupId"
        />
    </section>
</template>

