<script setup>

import { ref } from 'vue'
import { emailValidator, requiredValidator, phoneValidator } from '@validators'

const props = defineProps({
  genders: {
    type: Object,
    required: true
  },
  inmigrant: {
    type: Object,
    required: false
  }
})

const emit = defineEmits(['submit'])

const listGenders = ref(props.genders)
const refVForm = ref()

const userDetail = ref({
    name: '',
    last_name: '',
    email: '',
    phone: '',
    gender_id: '',
    birthdate: ''
})

watchEffect(fetchData)

async function fetchData() {

  if(props.inmigrant) {
    
    userDetail.value.name = props.inmigrant.name
    userDetail.value.last_name = props.inmigrant.last_name
    userDetail.value.email = props.inmigrant.email
    userDetail.value.phone = props.inmigrant.phone
    userDetail.value.gender_id = props.inmigrant.gender_id
    userDetail.value.birthdate = props.inmigrant.birthdate
  }
}

const startDateTimePickerConfig = computed(() => {

  const now = new Date();
  const tomorrow = new Date(now);
  tomorrow.setDate(now.getDate() + 1);

  const formatToISO = (date) => date.toISOString().split('T')[0];


  const config = {
    dateFormat: 'Y-m-d',
    disable: [
      {
        from: formatToISO(now),
        to: '2099-12-31' // Una fecha futura lejana para bloquear indefinidamente
      }
    ]
  }

  return config
})

const onSubmit = () => {
  refVForm.value?.validate().then(({ valid: isValid }) => {
    if (isValid) {
      emit('submit', userDetail.value)
    }
  })
}
</script>
<template>
  <VForm
    ref="refVForm"
    @submit.prevent="onSubmit"
    >
    <VCard
      title="General"
      class="mb-6"
    >
      <VCardText>
        <VRow>
          <VCol cols="12" md="6">
            <VTextField
              v-model="userDetail.name"
              :rules="[requiredValidator]"
              label="Nombre"
            />
          </VCol>
          <VCol cols="12" md="6">
            <VTextField
              v-model="userDetail.last_name"
              :rules="[requiredValidator]"
              label="Apellido"
            />
          </VCol>
          <VCol cols="12" md="12">
            <VTextField
              v-model="userDetail.email"
              label="E-mail"
              type="email"
              :rules="[requiredValidator,emailValidator]"
            />
          </VCol>
          <VCol cols="12" md="4">
            <VTextField
              v-model="userDetail.phone"
              type="tel"
              label="Teléfono"
              placeholder="+(XX) XXXXXXXXX"
              :rules="[phoneValidator, requiredValidator]"
            />
          </VCol>
          <VCol cols="12" md="4">
            <VAutocomplete
              v-model="userDetail.gender_id"
              label="Género"
              :items="listGenders"
              item-title="name"
              item-value="id"
              :menu-props="{ maxHeight: '200px' }"
              :rules="[requiredValidator]"
            />
          </VCol>
          <VCol cols="12" md="4">
            <AppDateTimePicker
              :key="JSON.stringify(startDateTimePickerConfig)"
              v-model="userDetail.birthdate"
              label="Fecha de Nacimiento"
              :rules="[requiredValidator]"
              :config="startDateTimePickerConfig"
              clearable
            />
          </VCol>
        </VRow>
      </VCardText>
    </VCard>

    <div class="d-flex justify-end gap-x-4">
      <VBtn type="submit">Siguiente</VBtn>
    </div>
  </VForm>
</template>
