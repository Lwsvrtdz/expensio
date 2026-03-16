<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import membersRoutes from '@/routes/groups/members';

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

const form = useForm({
    email: '',
});

function submitInvite() {
    form.post(membersRoutes.store({ id: props.groupId }).url, {
        onSuccess: () => form.reset('email'),
        preserveScroll: true,
    });
}
</script>

<template>
    <section
        class="grid gap-4 rounded-lg border border-sidebar-border/70 bg-background/60 p-4 shadow-sm dark:border-sidebar-border md:grid-cols-[minmax(0,2fr)_minmax(0,1.5fr)]"
    >
        <div class="space-y-3">
            <h2 class="text-sm font-semibold tracking-tight">
                Members
            </h2>

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
        </div>

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
                        v-model="form.email"
                        type="email"
                        name="email"
                        autocomplete="email"
                        placeholder="friend@example.com"
                    />
                    <InputError :message="form.errors.email" />
                </div>

                <div class="flex items-center gap-3">
                    <Button
                        type="submit"
                        size="sm"
                        :disabled="form.processing"
                    >
                        {{
                            form.processing
                                ? 'Sending...'
                                : 'Send invite'
                        }}
                    </Button>

                    <p
                        v-if="form.recentlySuccessful"
                        class="text-xs text-muted-foreground"
                    >
                        Invitation sent.
                    </p>
                </div>
            </form>
        </div>
    </section>
</template>

