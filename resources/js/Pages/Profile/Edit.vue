<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});
</script>

<template>
    <Head title="Meu Perfil - FreelanceTime" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col gap-1">
                <h2 class="text-2xl font-bold tracking-tight text-zinc-900">
                    Configurações de Perfil
                </h2>
                <p class="text-sm text-zinc-500">Gerencie suas informações pessoais e segurança da conta.</p>
            </div>
        </template>

        <div class="py-10 bg-zinc-50/50 min-h-[calc(100-64px)]">
            <div class="mx-auto max-w-7xl space-y-8 px-4 sm:px-6 lg:px-8">

                <div class="bg-white border-none shadow-sm shadow-zinc-200/50 ring-1 ring-zinc-200/60 rounded-2xl overflow-hidden p-6 sm:p-10 transition-all">
                    <UpdateProfileInformationForm
                        :must-verify-email="mustVerifyEmail"
                        :status="status"
                        class="max-w-3xl"
                    />
                </div>

                <div class="bg-white border-none shadow-sm shadow-zinc-200/50 ring-1 ring-zinc-200/60 rounded-2xl overflow-hidden p-6 sm:p-10 transition-all">
                    <UpdatePasswordForm class="max-w-3xl" />
                </div>

                <div class="bg-white border-none shadow-sm shadow-zinc-200/50 ring-1 ring-zinc-100 rounded-2xl overflow-hidden p-6 sm:p-10 transition-all border-l-4 border-l-red-50">
                    <DeleteUserForm class="max-w-3xl" />
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
/* Estilo para garantir que o layout preencha a tela suavemente */
:deep(section header h2) {
    @apply text-xl font-bold text-zinc-900;
}

:deep(section header p) {
    @apply text-sm text-zinc-500 mt-2;
}
</style>
