<script setup lang="ts">
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { Button } from '@/components/ui/button';
import inviteAcceptRoutes from '@/routes/invite/accept';

type Group = {
    id: string | number;
    name: string;
};

const props = defineProps<{
    group: Group;
    token: string;
}>();

const page = usePage<{ auth: { user: { name?: string } | null } }>();
const authUser = computed(() => page.props.auth.user);

const form = useForm({});

function accept() {
    form.post(inviteAcceptRoutes.store(props.token).url, {
        preserveScroll: true,
    });
}
</script>

<template>
    <Head title="Join group" />

    <main
        class="flex min-h-screen items-center justify-center bg-background px-4 py-12"
    >
        <section
            class="w-full max-w-md rounded-xl border border-sidebar-border/70 bg-background/80 p-6 shadow-sm dark:border-sidebar-border"
        >
            <h1 class="text-lg font-semibold tracking-tight">
                Join "{{ group.name }}"
            </h1>

            <p class="mt-2 text-sm text-muted-foreground">
                {{
                    authUser
                        ? `You’re about to join this group as ${
                              authUser.name ?? 'this account'
                          }.`
                        : 'Create or log into an account to join this group and track shared expenses.'
                }}
            </p>

            <form @submit.prevent="accept" class="mt-6 space-y-4">
                <Button
                    type="submit"
                    class="w-full"
                    :disabled="form.processing"
                >
                    {{
                        form.processing
                            ? 'Joining...'
                            : authUser
                              ? 'Join this group'
                              : 'Continue to join'
                    }}
                </Button>

            <p class="text-xs text-muted-foreground">
                If you don’t have an account yet, you’ll be asked to
                register first. Your invitation will be applied
                automatically.
            </p>
            </form>
        </section>
    </main>
</template>

