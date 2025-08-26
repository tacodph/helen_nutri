<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthBase from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

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
    <AuthBase title="Criar uma conta" description="Digite seus dados abaixo para criar sua conta" class="bg-fundo-escuro">
        <Head title="Register" />

        <form @submit.prevent="submit" class="flex flex-col gap-6">
            <div class="grid gap-6">
                <div class="grid gap-2">
                    <Label for="name" class="text-branco">Nome</Label>
                    <Input 
                        id="name" 
                        type="text" 
                        required 
                        autofocus 
                        :tabindex="1" 
                        autocomplete="name" 
                        v-model="form.name" 
                        placeholder="Nome completo"
                        class="bg-cinza border-branco/10 text-branco placeholder:text-branco/50" 
                    />
                    <InputError :message="form.errors.name" />
                </div>

                <div class="grid gap-2">
                    <Label for="email" class="text-branco">Email</Label>
                    <Input 
                        id="email" 
                        type="email" 
                        required 
                        :tabindex="2" 
                        autocomplete="email" 
                        v-model="form.email" 
                        placeholder="seu@email.com"
                        class="bg-cinza border-branco/10 text-branco placeholder:text-branco/50" 
                    />
                    <InputError :message="form.errors.email" />
                </div>

                <div class="grid gap-2">
                    <Label for="password" class="text-branco">Senha</Label>
                    <Input
                        id="password"
                        type="password"
                        required
                        :tabindex="3"
                        autocomplete="new-password"
                        v-model="form.password"
                        placeholder="Sua senha"
                        class="bg-cinza border-branco/10 text-branco placeholder:text-branco/50"
                    />
                    <InputError :message="form.errors.password" />
                </div>

                <div class="grid gap-2">
                    <Label for="password_confirmation" class="text-branco">Confirmar senha</Label>
                    <Input
                        id="password_confirmation"
                        type="password"
                        required
                        :tabindex="4"
                        autocomplete="new-password"
                        v-model="form.password_confirmation"
                        placeholder="Confirme sua senha"
                        class="bg-cinza border-branco/10 text-branco placeholder:text-branco/50"
                    />
                    <InputError :message="form.errors.password_confirmation" />
                </div>

                <Button 
                    type="submit" 
                    class="mt-2 w-full bg-verde-primario hover:bg-verde-primario/90 text-fundo-escuro cursor-pointer" 
                    tabindex="5" 
                    :disabled="form.processing"
                >
                    <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                    Criar conta
                </Button>
            </div>

            <div class="text-center text-sm text-branco/70">
                Já tem uma conta?
                <TextLink 
                    :href="route('login')" 
                    :tabindex="6"
                    class="text-verde-primario hover:text-verde-primario/90"
                >
                    Entrar
                </TextLink>
            </div>
        </form>
    </AuthBase>
</template>
