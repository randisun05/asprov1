<template>
  <Head>
    <title>Verifikasi Dua Langkah</title>
  </Head>

  <section id="our-blog" class="padding text-center">
    <div class="container">
      <div class="row d-flex justify-content-center">
        <div class="col-lg-8 col-md-8 col-sm-12">
          <div class="bglight logincontainer">
            <h3 class="darkcolor bottom35">Verifikasi Dua Langkah</h3>
            <p class="mb-4">
              {{ useRecovery
                ? 'Masukkan salah satu recovery code yang pernah Anda simpan.'
                : 'Masukkan kode 6 digit dari aplikasi authenticator Anda.' }}
            </p>

            <form @submit.prevent="submit" class="getin_form border-form">
              <div class="row">
                <div class="col-md-12 col-sm-12" v-if="!useRecovery">
                  <div class="form-group bottom35">
                    <input
                      type="text"
                      inputmode="numeric"
                      class="form-control"
                      v-model="form.code"
                      placeholder="Kode 6 digit"
                      autocomplete="one-time-code"
                      autofocus
                    />
                    <div v-if="errors?.code" class="alert alert-danger mt-2">{{ errors.code }}</div>
                  </div>
                </div>

                <div class="col-md-12 col-sm-12" v-else>
                  <div class="form-group bottom35">
                    <input
                      type="text"
                      class="form-control"
                      v-model="form.recovery_code"
                      placeholder="Recovery code"
                      autofocus
                    />
                    <div v-if="errors?.recovery_code" class="alert alert-danger mt-2">{{ errors.recovery_code }}</div>
                  </div>
                </div>

                <div class="col-sm-12">
                  <button type="submit" class="button btnprimary w-100" :disabled="submitting">
                    {{ submitting ? 'Memproses…' : 'Verifikasi' }}
                  </button>
                </div>

                <div class="col-sm-12 mt-3">
                  <a href="#" @click.prevent="useRecovery = !useRecovery">
                    {{ useRecovery ? 'Gunakan kode authenticator' : 'Gunakan recovery code' }}
                  </a>
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
import { Head } from '@inertiajs/inertia-vue3'
import { Inertia } from '@inertiajs/inertia'
import { reactive, ref } from 'vue'

defineProps({
  errors: { type: Object, default: () => ({}) },
})

const useRecovery = ref(false)
const submitting = ref(false)

const form = reactive({
  code: '',
  recovery_code: '',
})

const submit = () => {
  submitting.value = true

  Inertia.post('/two-factor-challenge', {
    code: useRecovery.value ? '' : form.code,
    recovery_code: useRecovery.value ? form.recovery_code : '',
  }, {
    onFinish: () => (submitting.value = false),
  })
}
</script>

<script>
import LayoutAuth from '../../Layouts/Auth.vue'
export default { layout: LayoutAuth }
</script>
