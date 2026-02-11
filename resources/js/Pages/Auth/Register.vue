<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Criar Conta - FreelanceTime" />

    <div class="min-h-screen bg-white text-zinc-900 flex flex-col justify-center relative overflow-hidden py-12">
        <div class="absolute inset-0 -z-10">
            <div class="absolute top-0 right-0 -translate-y-1/4 translate-x-1/4 w-[600px] h-[600px] rounded-full bg-emerald-50 blur-3xl opacity-60"></div>
            <div class="absolute bottom-0 left-0 translate-y-1/4 -translate-x-1/4 w-[600px] h-[600px] rounded-full bg-zinc-100 blur-3xl opacity-60"></div>
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
                <div class="mb-8 text-center sm:text-left">
                    <h1 class="text-2xl font-bold text-zinc-900 text-balance">Comece a controlar seu tempo agora</h1>
                    <p class="text-zinc-500 mt-2">Crie sua conta gratuita em poucos segundos.</p>
                </div>

                <form @submit.prevent="submit" class="space-y-4">
                    <div>
                        <InputLabel for="name" value="Nome Completo" class="text-zinc-700 font-medium" />
                        <TextInput
                            id="name"
                            type="text"
                            class="mt-1.5 block w-full !rounded-xl !border-zinc-200 focus:!border-emerald-500 focus:!ring-emerald-500 transition-all shadow-sm"
                            v-model="form.name"
                            placeholder="Como quer ser chamado?"
                            required
                            autofocus
                            autocomplete="name"
                        />
                        <InputError class="mt-1" :message="form.errors.name" />
                    </div>

                    <div>
                        <InputLabel for="email" value="Email Profissional" class="text-zinc-700 font-medium" />
                        <TextInput
                            id="email"
                            type="email"
                            class="mt-1.5 block w-full !rounded-xl !border-zinc-200 focus:!border-emerald-500 focus:!ring-emerald-500 transition-all shadow-sm"
                            v-model="form.email"
                            placeholder="exemplo@email.com"
                            required
                            autocomplete="username"
                        />
                        <InputError class="mt-1" :message="form.errors.email" />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <InputLabel for="password" value="Senha" class="text-zinc-700 font-medium" />
                            <TextInput
                                id="password"
                                type="password"
                                class="mt-1.5 block w-full !rounded-xl !border-zinc-200 focus:!border-emerald-500 focus:!ring-emerald-500 transition-all shadow-sm"
                                v-model="form.password"
                                placeholder="••••••••"
                                required
                                autocomplete="new-password"
                            />
                        </div>
                        <div>
                            <InputLabel for="password_confirmation" value="Confirmar" class="text-zinc-700 font-medium" />
                            <TextInput
                                id="password_confirmation"
                                type="password"
                                class="mt-1.5 block w-full !rounded-xl !border-zinc-200 focus:!border-emerald-500 focus:!ring-emerald-500 transition-all shadow-sm"
                                v-model="form.password_confirmation"
                                placeholder="••••••••"
                                required
                                autocomplete="new-password"
                            />
                        </div>
                        <InputError class="col-span-full mt-1" :message="form.errors.password" />
                        <InputError class="col-span-full mt-1" :message="form.errors.password_confirmation" />
                    </div>

                    <div class="pt-2">
                        <button
                            class="w-full flex items-center justify-center rounded-xl bg-zinc-900 px-4 py-3.5 text-sm font-semibold text-white hover:bg-zinc-800 focus:outline-none focus:ring-2 focus:ring-zinc-900 focus:ring-offset-2 transition-all disabled:opacity-50 shadow-lg shadow-zinc-900/10"
                            :class="{ 'opacity-50': form.processing }"
                            :disabled="form.processing"
                        >
                            <span v-if="!form.processing">Criar minha conta</span>
                            <span v-else>Processando...</span>
                            <svg v-if="!form.processing" class="ms-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                            </svg>
                        </button>
                    </div>
                </form>

                <div class="mt-8 text-center border-t border-zinc-100 pt-6">
                    <p class="text-sm text-zinc-600">
                        Já tem uma conta?
                        <Link :href="route('login')" class="font-bold text-zinc-900 hover:text-emerald-600 transition-colors underline-offset-4 hover:underline">
                            Fazer login
                        </Link>
                    </p>
                </div>
            </div>
        </div>

        <p class="mt-10 text-center text-xs text-zinc-400">
            Ao se cadastrar, você concorda com nossos <br class="sm:hidden">
            <a href="#" class="underline">Termos de Serviço</a> e <a href="#" class="underline">Política de Privacidade</a>.
        </p>
    </div>
</template>
