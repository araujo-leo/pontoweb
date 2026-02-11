<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const user = usePage().props.auth.user;

const form = useForm({
    name: user.name,
    email: user.email,
});
</script>

<template>
    <section>
        <header>
            <h2 class="text-xl font-bold text-zinc-900">
                Informações do Perfil
            </h2>

            <p class="mt-2 text-sm text-zinc-500 leading-relaxed max-w-2xl">
                Mantenha seus dados atualizados para que seus relatórios e faturas contenham as informações corretas.
            </p>
        </header>

        <form
            @submit.prevent="form.patch(route('profile.update'))"
            class="mt-8 space-y-6 max-w-xl"
        >
            <div>
                <InputLabel for="name" value="Nome Completo" class="text-zinc-700 font-medium" />

                <TextInput
                    id="name"
                    type="text"
                    class="mt-1.5 block w-full !rounded-xl !border-zinc-200 focus:!border-emerald-500 focus:!ring-emerald-500 transition-all shadow-sm"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                    placeholder="Seu nome"
                />

                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div>
                <InputLabel for="email" value="E-mail Profissional" class="text-zinc-700 font-medium" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1.5 block w-full !rounded-xl !border-zinc-200 focus:!border-emerald-500 focus:!ring-emerald-500 transition-all shadow-sm"
                    v-model="form.email"
                    required
                    autocomplete="username"
                    placeholder="exemplo@email.com"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div v-if="mustVerifyEmail && user.email_verified_at === null" class="p-4 bg-amber-50 rounded-2xl border border-amber-100">
                <p class="text-sm text-amber-800 flex items-center gap-2">
                    <svg class="h-4 w-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    Seu endereço de e-mail não foi verificado.
                </p>

                <Link
                    :href="route('verification.send')"
                    method="post"
                    as="button"
                    class="mt-2 text-sm font-bold text-amber-900 underline underline-offset-4 hover:text-amber-700 transition"
                >
                    Clique aqui para reenviar o link de verificação.
                </Link>

                <div
                    v-show="status === 'verification-link-sent'"
                    class="mt-2 text-sm font-medium text-emerald-600"
                >
                    Um novo link de verificação foi enviado.
                </div>
            </div>

            <div class="flex items-center gap-4 pt-2">
                <button
                    type="submit"
                    class="inline-flex items-center justify-center rounded-xl bg-zinc-900 px-6 py-3 text-sm font-semibold text-white hover:bg-zinc-800 focus:outline-none focus:ring-2 focus:ring-zinc-900 focus:ring-offset-2 transition-all shadow-lg shadow-zinc-900/10 disabled:opacity-50"
                    :disabled="form.processing"
                >
                    Salvar Informações
                </button>

                <Transition
                    enter-active-class="transition ease-in-out duration-300"
                    enter-from-class="opacity-0 translate-x-2"
                    enter-to-class="opacity-100 translate-x-0"
                    leave-active-class="transition ease-in-out duration-300"
                    leave-to-class="opacity-0"
                >
                    <p
                        v-if="form.recentlySuccessful"
                        class="text-sm font-medium text-emerald-600 flex items-center gap-1"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        Perfil atualizado!
                    </p>
                </Transition>
            </div>
        </form>
    </section>
</template>
