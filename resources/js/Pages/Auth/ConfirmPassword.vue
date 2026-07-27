<template>
  <Head>
    <title>Konfirmasi Password</title>
  </Head>

  <section id="our-blog" class="padding text-center">
    <div class="container">
      <div class="row d-flex justify-content-center">
        <div class="col-lg-8 col-md-8 col-sm-12">
          <div class="bglight logincontainer">
            <h3 class="darkcolor bottom35">Konfirmasi Password</h3>
            <p class="mb-4">Ini adalah area sensitif. Masukkan kembali password Anda untuk melanjutkan.</p>

            <form @submit.prevent="submit" class="getin_form border-form">
              <div class="row">
                <div class="col-md-12 col-sm-12">
                  <div class="form-group bottom35">
                    <input
                      type="password"
                      class="form-control"
                      v-model="form.password"
                      placeholder="Password"
                      autocomplete="current-password"
                      autofocus
                    />
                    <div v-if="errors?.password" class="alert alert-danger mt-2">{{ errors.password }}</div>
                  </div>
                </div>

                <div class="col-sm-12">
                  <button type="submit" class="button btnprimary w-100" :disabled="submitting">
                    {{ submitting ? 'Memproses…' : 'Konfirmasi' }}
                  </button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { Head } from '@inertiajs/vue3'
import { router } from '@inertiajs/vue3';
import { reactive, ref } from 'vue'

defineProps({
  errors: { type: Object, default: () => ({}) },
})

const submitting = ref(false)
const form = reactive({ password: '' })

const submit = () => {
  submitting.value = true
  router.post('/user/confirm-password', form, {
    onFinish: () => (submitting.value = false),
  })
}
</script>

<script>
import LayoutAuth from '../../Layouts/Auth.vue'
export default { layout: LayoutAuth }
</script>
