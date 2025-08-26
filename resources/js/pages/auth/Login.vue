<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthBase from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

defineProps<{
    status?: string;
    canResetPassword: boolean;
}>();

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
    <AuthBase title="Entre na sua conta" description="Digite seu email e senha para acessar sua conta" class="bg-fundo-escuro">
        <Head title="Login" />

        <div v-if="status" class="mb-4 text-center text-sm font-medium text-green-500">
            {{ status }}
        </div>

            <form @submit.prevent="submit" class="flex flex-col gap-6">
                <div class="grid gap-6">
                    <div class="grid gap-2">
                        <Label for="email" class="text-branco">Email</Label>
                        <Input
                            id="email"
                            type="email"
                            required
                            autofocus
                            :tabindex="1"
                            autocomplete="email"
                            v-model="form.email"
                            placeholder="seu@email.com"
                            class="bg-cinza border-branco/10 text-branco placeholder:text-branco/50"
                        />
                        <InputError :message="form.errors.email" />
                    </div>

                    <div class="grid gap-2">
                        <div class="flex items-center justify-between">
                            <Label for="password" class="text-branco">Senha</Label>
                            <TextLink 
                                v-if="canResetPassword" 
                                :href="route('password.request')" 
                                class="text-sm text-verde-primario hover:text-verde-primario/90" 
                                :tabindex="5"
                            >
                                Esqueceu a senha?
                            </TextLink>
                        </div>
                        <Input
                            id="password"
                            type="password"
                            required
                            :tabindex="2"
                            autocomplete="current-password"
                            v-model="form.password"
                            placeholder="Sua senha"
                            class="bg-cinza border-branco/10 text-branco placeholder:text-branco/50"
                        />
                        <InputError :message="form.errors.password" />
                    </div>

                    <div class="flex items-center space-x-3">
                        <Checkbox 
                            id="remember" 
                            v-model="form.remember" 
                            :tabindex="3"
                            class="border-branco/30 data-[state=checked]:bg-verde-primario data-[state=checked]:border-verde-primario data-[state=checked]:text-fundo-escuro"
                        />
                        <Label for="remember" class="text-branco">Lembrar de mim</Label>
                    </div>

                    <Button 
                        type="submit" 
                        class="w-full bg-verde-primario hover:bg-verde-primario/90 text-fundo-escuro cursor-pointer" 
                        :tabindex="4" 
                        :disabled="form.processing"
                    >
                        <LoaderCircle v-if="form.processing" class="mr-2 h-4 w-4 animate-spin" />
                        Entrar
                    </Button>
                </div>

                <div class="text-center text-sm text-branco/70">
                    Não tem uma conta?
                    <TextLink 
                        :href="route('register')" 
                        :tabindex="5"
                        class="text-verde-primario hover:text-verde-primario/90"
                    >
                        Criar conta
                    </TextLink>
                </div>
            </form>
    </AuthBase>
</template>
