<script setup lang="ts">
import { computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import Dialog from '@/components/ui/dialog/Dialog.vue';
import DialogContent from '@/components/ui/dialog/DialogContent.vue';
import DialogHeader from '@/components/ui/dialog/DialogHeader.vue';
import DialogTitle from '@/components/ui/dialog/DialogTitle.vue';
import DialogDescription from '@/components/ui/dialog/DialogDescription.vue';
import membersRoutes from '@/routes/groups/members';

const props = defineProps<{
    groupId: string;
    modelValue: boolean;
}>();

const emit = defineEmits<{
    (e: 'update:modelValue', value: boolean): void;
}>();

const isOpen = computed({
    get: () => props.modelValue,
    set: (value: boolean) => emit('update:modelValue', value),
});

const form = useForm({
    email: '',
});

function submitInvite() {
    form.post(membersRoutes.store({ id: props.groupId }).url, {
        onSuccess: () => {
            form.reset('email');
            isOpen.value = false;
        },
        preserveScroll: true,
    });
}
</script>

<template>
    <Dialog :open="isOpen" @update:open="isOpen = $event">
        <DialogContent class="sm:max-w-sm">
            <DialogHeader>
                <DialogTitle>Invite members</DialogTitle>
                <DialogDescription>
                    Invite someone by email. Existing users are added
                    immediately; others receive an email invitation.
                </DialogDescription>
            </DialogHeader>

            <form class="space-y-4" @submit.prevent="submitInvite">
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
                        class="flex-1"
                        :disabled="form.processing"
                    >
                        {{
                            form.processing
                                ? 'Sending...'
                                : 'Send invite'
                        }}
                    </Button>
                </div>
            </form>
        </DialogContent>
    </Dialog>
</template>

