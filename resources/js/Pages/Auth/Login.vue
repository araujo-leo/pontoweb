<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Entrar - FreelanceTime" />

    <div class="min-h-screen bg-white text-zinc-900 flex flex-col justify-center relative overflow-hidden">
        <div class="absolute inset-0 -z-10">
            <div class="absolute top-0 right-0 -translate-y-1/4 translate-x-1/4 w-[500px] h-[500px] rounded-full bg-emerald-50 blur-3xl opacity-60"></div>
            <div class="absolute bottom-0 left-0 translate-y-1/4 -translate-x-1/4 w-[500px] h-[500px] rounded-full bg-zinc-100 blur-3xl opacity-60"></div>
        </div>

        <div class="sm:mx-auto sm:w-full sm:max-w-md px-4">
            <Link href="/" class="flex justify-center items-center gap-2 mb-8 group">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-zinc-900 group-hover:bg-emerald-600 transition-colors duration-300">
                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2m6-2a10 10 0 11-20 0 10 10 0 0120 0z" />
                    </svg>
                </div>
                <span class="text-2xl font-bold tracking-tight text-zinc-900">FreelanceTime</span>
            </Link>

            <div class="bg-white/70 backdrop-blur-xl border border-zinc-100 shadow-xl shadow-zinc-200/50 rounded-3xl p-8 sm:p-10">
                <div class="mb-8">
                    <h1 class="text-2xl font-bold text-zinc-900">Bem-vindo de volta</h1>
                    <p class="text-zinc-500 mt-2">Insira seus dados para acessar sua conta.</p>
                </div>

                <div v-if="status" class="mb-4 text-sm font-medium text-emerald-600 bg-emerald-50 p-3 rounded-lg">
                    {{ status }}
                </div>

                <form @submit.prevent="submit" class="space-y-5">
                    <div>
                        <InputLabel for="email" value="Email" class="text-zinc-700 font-medium" />
                        <TextInput
                            id="email"
                            type="email"
                            class="mt-1.5 block w-full !rounded-xl !border-zinc-200 focus:!border-emerald-500 focus:!ring-emerald-500 transition-all shadow-sm"
                            v-model="form.email"
                            placeholder="seu@email.com"
                            required
                            autofocus
                            autocomplete="username"
                        />
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <div>
                        <div class="flex items-center justify-between">
                            <InputLabel for="password" value="Senha" class="text-zinc-700 font-medium" />
                            <Link
                                v-if="canResetPassword"
                                :href="route('password.request')"
                                class="text-xs font-semibold text-emerald-600 hover:text-emerald-700"
                            >
                                Esqueceu a senha?
                            </Link>
                        </div>
                        <TextInput
                            id="password"
                            type="password"
                            class="mt-1.5 block w-full !rounded-xl !border-zinc-200 focus:!border-emerald-500 focus:!ring-emerald-500 transition-all shadow-sm"
                            v-model="form.password"
                            placeholder="••••••••"
                            required
                            autocomplete="current-password"
                        />
                        <InputError class="mt-2" :message="form.errors.password" />
                    </div>

                    <div class="flex items-center">
                        <Checkbox name="remember" v-model:checked="form.remember" class="!rounded !text-emerald-600 focus:!ring-emerald-500" />
                        <span class="ms-2 text-sm text-zinc-600">Lembrar de mim</span>
                    </div>

                    <button
                        class="w-full flex items-center justify-center rounded-xl bg-zinc-900 px-4 py-3.5 text-sm font-semibold text-white hover:bg-zinc-800 focus:outline-none focus:ring-2 focus:ring-zinc-900 focus:ring-offset-2 transition-all disabled:opacity-50"
                        :class="{ 'opacity-50': form.processing }"
                        :disabled="form.processing"
                    >
                        <span>Entrar na conta</span>
                        <svg v-if="!form.processing" class="ms-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                        </svg>
                        <svg v-else class="animate-spin ms-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </button>
                </form>

                <div class="mt-8 text-center">
                    <p class="text-sm text-zinc-600">
                        Não tem uma conta?
                        <Link :href="route('register')" class="font-bold text-zinc-900 hover:text-emerald-600 transition-colors">
                            Cadastre-se grátis
                        </Link>
                    </p>
                </div>
            </div>
        </div>

        <div class="mt-12 text-center text-xs text-zinc-400">
            &copy; 2026 FreelanceTime. Todos os direitos reservados.
        </div>
    </div>
</template>

<style scoped>
/* Remove o foco azul padrão para manter a paleta Emerald */
input:focus {
    outline: none;
}
</style>
