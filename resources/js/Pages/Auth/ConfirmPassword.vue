<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

const form = useForm({
    password: '',
});

const submit = () => {
    form.post(route('password.confirm'), {
        onFinish: () => form.reset(),
    });
};
</script>

<template>
    <Head title="Confirmar Senha - FreelanceTime" />

    <div class="min-h-screen bg-white text-zinc-900 flex flex-col justify-center relative overflow-hidden py-12">
        <div class="absolute inset-0 -z-10">
            <div class="absolute top-0 right-0 -translate-y-1/4 translate-x-1/4 w-[500px] h-[500px] rounded-full bg-emerald-50 blur-3xl opacity-60"></div>
            <div class="absolute bottom-0 left-0 translate-y-1/4 -translate-x-1/4 w-[500px] h-[500px] rounded-full bg-zinc-100 blur-3xl opacity-60"></div>
        </div>

        <div class="sm:mx-auto sm:w-full sm:max-w-md px-4">
            <Link href="/" class="flex justify-center items-center gap-2 mb-8 group">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-zinc-900 group-hover:bg-emerald-600 transition-colors duration-300">
                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 11V7a2 2 0 00-2-2 2 2 0 00-2 2v4" />
                        <rect x="5" y="11" width="8" height="6" rx="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </div>
                <span class="text-2xl font-bold tracking-tight text-zinc-900">FreelanceTime</span>
            </Link>

            <div class="bg-white/70 backdrop-blur-xl border border-zinc-100 shadow-xl shadow-zinc-200/50 rounded-3xl p-8 sm:p-10">
                <div class="mb-8 text-center sm:text-left">
                    <div class="inline-flex items-center justify-center h-12 w-12 rounded-full bg-emerald-50 text-emerald-600 mb-4">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <h1 class="text-2xl font-bold text-zinc-900">Área Segura</h1>
                    <p class="text-zinc-500 mt-2 text-pretty leading-relaxed">
                        Esta é uma área segura. Por favor, confirme sua senha antes de continuar.
                    </p>
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <div>
                        <InputLabel for="password" value="Sua Senha" class="text-zinc-700 font-medium" />
                        <TextInput
                            id="password"
                            type="password"
                            class="mt-1.5 block w-full !rounded-xl !border-zinc-200 focus:!border-emerald-500 focus:!ring-emerald-500 transition-all shadow-sm"
                            v-model="form.password"
                            placeholder="Insira sua senha atual"
                            required
                            autocomplete="current-password"
                            autofocus
                        />
                        <InputError class="mt-2" :message="form.errors.password" />
                    </div>

                    <div class="pt-2">
                        <button
                            class="w-full flex items-center justify-center rounded-xl bg-zinc-900 px-4 py-3.5 text-sm font-semibold text-white hover:bg-zinc-800 focus:outline-none focus:ring-2 focus:ring-zinc-900 focus:ring-offset-2 transition-all disabled:opacity-50 shadow-lg shadow-zinc-900/10"
                            :class="{ 'opacity-50': form.processing }"
                            :disabled="form.processing"
                        >
                            <span v-if="!form.processing">Confirmar Acesso</span>
                            <span v-else>Verificando...</span>
                            <svg v-if="!form.processing" class="ms-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </button>
                    </div>
                </form>

                <div class="mt-8 text-center">
                    <Link :href="route('dashboard')" class="text-sm font-semibold text-zinc-500 hover:text-zinc-900 transition-colors">
                        Cancelar e voltar
                    </Link>
                </div>
            </div>
        </div>

        <div class="mt-12 text-center text-xs text-zinc-400">
            Proteção de conta via FreelanceTime Security
        </div>
    </div>
</template>
