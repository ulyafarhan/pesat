<script setup>
import { useForm, Head } from '@inertiajs/vue3';

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

function submit() {
    form.post('/login', {
        onFinish: () => form.reset('password'),
    });
}
</script>

<template>
    <Head title="Login Petugas WH" />
    <div
        class="flex min-h-screen flex-col items-center justify-center bg-gray-50 px-4 font-sans sm:px-6 lg:px-8"
    >
        <div
            class="w-full max-w-md space-y-8 rounded-lg border border-neutral-border bg-white p-8 shadow-none sm:p-10"
        >
            <div class="space-y-2 text-center">
                <div
                    class="mb-2 inline-flex h-10 w-10 items-center justify-center rounded-full bg-primary text-sm font-bold text-white"
                >
                    P
                </div>
                <h2
                    class="font-sans text-2xl font-normal tracking-tight text-primary"
                >
                    PESAT Control Center
                </h2>
                <p class="font-sans text-xs font-light text-secondary">
                    Silakan masuk untuk mengakses panel pengawasan Wilayatul
                    Hisbah
                </p>
            </div>

            <form @submit.prevent="submit" class="space-y-6">
                <div class="space-y-2">
                    <label
                        for="email"
                        class="block font-sans text-xs font-semibold text-secondary uppercase"
                        >Alamat Email</label
                    >
                    <input
                        id="email"
                        type="email"
                        v-model="form.email"
                        required
                        placeholder="nama@pesat.local"
                        class="w-full rounded-full border border-neutral-border bg-white px-6 py-3.5 text-sm text-primary placeholder-gray-400 transition duration-200 focus:border-primary focus:outline-none"
                    />
                    <span
                        v-if="form.errors.email"
                        class="mt-1 block font-sans text-xs text-error"
                        >{{ form.errors.email }}</span
                    >
                </div>

                <div class="space-y-2">
                    <label
                        for="password"
                        class="block font-sans text-xs font-semibold text-secondary uppercase"
                        >Kata Sandi</label
                    >
                    <input
                        id="password"
                        type="password"
                        v-model="form.password"
                        required
                        placeholder="••••••••"
                        class="w-full rounded-full border border-neutral-border bg-white px-6 py-3.5 text-sm text-primary placeholder-gray-400 transition duration-200 focus:border-primary focus:outline-none"
                    />
                    <span
                        v-if="form.errors.password"
                        class="mt-1 block font-sans text-xs text-error"
                        >{{ form.errors.password }}</span
                    >
                </div>

                <div class="flex items-center justify-between font-sans">
                    <label
                        class="flex cursor-pointer items-center space-x-2 text-xs text-secondary select-none"
                    >
                        <input
                            type="checkbox"
                            v-model="form.remember"
                            class="cursor-pointer rounded border-neutral-border text-primary focus:ring-0"
                        />
                        <span>Ingat saya di perangkat ini</span>
                    </label>
                </div>

                <div>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full rounded-full bg-primary py-4 text-center text-xs font-bold tracking-wider text-white uppercase transition duration-200 hover:bg-secondary disabled:opacity-50"
                    >
                        <span>{{
                            form.processing
                                ? 'Memverifikasi...'
                                : 'Masuk ke Panel'
                        }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
