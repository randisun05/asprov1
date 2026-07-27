<template>
  <Head>
    <title>Login Administrator</title>
  </Head>

  <section id="our-blog" class="padding text-center">
    <div class="container">
      <div class="row d-flex justify-content-center">
        <div class="col-lg-8 col-md-8 col-sm-12">
          <div class="bglight logincontainer">
            <h3 class="darkcolor bottom35">Login Administrator</h3>

            <!-- ⚠️ Alert error / warning -->
            <div v-if="alertMessage" class="alert" :class="alertClass" role="alert">
              {{ alertMessage }}
            </div>

            <form @submit.prevent="submit" class="getin_form border-form" id="login">
              <div class="row">

                <!-- EMAIL -->
                <div class="col-md-12 col-sm-12">
                  <div class="form-group bottom35">
                    <div class="input-group">
                      <span class="input-group-text"><i class="fa fa-id-badge"></i></span>
                      <input
                        type="email"
                        class="form-control"
                        v-model="form.email"
                        placeholder="Email Address"
                        required
                        autocomplete="username"
                      />
                    </div>
                    <div v-if="errors?.email" class="alert alert-danger mt-2">{{ errors.email }}</div>
                  </div>
                </div>

                <!-- PASSWORD -->
                <div class="col-md-12 col-sm-12">
                  <div class="form-group bottom35">
                    <div class="input-group">
                      <span class="input-group-text"><i class="fa fa-lock"></i></span>
                      <input
                        type="password"
                        placeholder="Password"
                        class="form-control"
                        v-model="form.password"
                        required
                        autocomplete="current-password"
                      />
                    </div>
                    <div v-if="errors?.password" class="alert alert-danger mt-2">{{ errors.password }}</div>
                  </div>
                </div>

                <!-- ✅ reCAPTCHA widget -->
                <!-- <div class="form-group mb-4 text-center">
                  <div id="recaptcha-container" class="g-recaptcha" :data-sitekey="sitekey"></div>
                  <div v-if="errors?.recaptcha_token" class="alert alert-danger mt-2">
                    {{ errors.recaptcha_token }}
                  </div>
                </div> -->

                <!-- SUBMIT -->
                <div class="col-sm-12">
                  <button
                    type="submit"
                    class="button btnprimary w-100"
                    :disabled="submitting"
                  >
                    {{ submitting ? 'Memproses…' : 'Login' }}
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

<!-- <script setup>
import { Head } from '@inertiajs/vue3'
import { router } from '@inertiajs/vue3';
import { reactive, ref, onMounted, nextTick } from 'vue'

const props = defineProps({
  errors: { type: Object, default: () => ({}) },
  session: { type: Object, default: () => ({}) },
})

const form = reactive({
  email: '',
  password: '',
  recaptcha_token: '',
})

const submitting = ref(false)
const sitekey = import.meta.env.VITE_RECAPTCHA_SITE_KEY
let widgetId = null

// 🔔 Alert management
const alertMessage = ref('')
const alertClass = ref('alert-warning')
const showAlert = (message, type = 'warning', timeout = 3000) => {
  alertMessage.value = message
  alertClass.value = `alert-${type}`
  setTimeout(() => (alertMessage.value = ''), timeout)
}

// ✅ Tunggu script reCAPTCHA siap
const waitForRecaptcha = () =>
  new Promise((resolve) => {
    if (window.grecaptcha?.render) return resolve()
    const interval = setInterval(() => {
      if (window.grecaptcha?.render) {
        clearInterval(interval)
        resolve()
      }
    }, 200)
  })

onMounted(async () => {
  await nextTick()
  await waitForRecaptcha()
  const container = document.getElementById('recaptcha-container')

  if (container && !widgetId) {
    try {
      widgetId = grecaptcha.render(container, {
        sitekey,
        callback: (token) => {
          form.recaptcha_token = token
        },
        'expired-callback': () => {
          form.recaptcha_token = ''
          showAlert('Token reCAPTCHA telah kadaluarsa, silakan centang ulang.', 'warning', 5000)
        },
        'error-callback': () => {
          form.recaptcha_token = ''
          showAlert('Terjadi kesalahan pada reCAPTCHA, silakan ulangi.', 'danger', 5000)
        },
      })
    } catch (e) {
      console.warn('Gagal render reCAPTCHA:', e.message)
    }
  }
})

const submit = async () => {
  // ✅ Validasi manual sebelum kirim
  if (!form.email) {
    showAlert('Email belum diisi!', 'warning')
    return
  }
  if (!form.password) {
    showAlert('Password belum diisi!', 'warning')
    return
  }
  if (!form.recaptcha_token) {
    showAlert('Mohon centang reCAPTCHA terlebih dahulu.', 'warning')
    return
  }

  submitting.value = true

  router.post('/login', form, {
    onFinish: () => {
      submitting.value = false
      // ✅ Reset reCAPTCHA aman
      try {
        if (widgetId !== null && window.grecaptcha) {
          grecaptcha.reset(widgetId)
          form.recaptcha_token = ''
        }
      } catch (e) {
        console.warn('reCAPTCHA belum siap untuk reset:', e.message)
      }
    },
  })
}
</script> -->
<script setup>
import { Head } from '@inertiajs/vue3'
import { router } from '@inertiajs/vue3';
import { reactive, ref } from 'vue'

const form = reactive({
  email: '',
  password: '',
  recaptcha_token: '',
})

const submitting = ref(false)
const sitekey = import.meta.env.VITE_RECAPTCHA_SITE_KEY

const submit = async () => {
  if (!form.email) return alert('Email belum diisi!')
  if (!form.password) return alert('Password belum diisi!')

  submitting.value = true

  // ✅ Jalankan reCAPTCHA v3
  grecaptcha.ready(async () => {
    try {
      const token = await grecaptcha.execute(sitekey, { action: 'submit' })
      form.recaptcha_token = token

      router.post('/login', form, {
        onFinish: () => (submitting.value = false),
      })
    } catch (err) {
      alert('Gagal memproses reCAPTCHA. Silakan refresh halaman.')
      submitting.value = false
    }
  })
}
</script>



<script>
import LayoutAuth from '../../Layouts/Auth.vue'
export default { layout: LayoutAuth }
</script>
