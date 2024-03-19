<script setup>

import { ref } from 'vue'
import {  requiredValidator, phoneValidator } from '@validators'

const props = defineProps({
  migrant: {
    type: Object,
    required: false
  }
})

const emit = defineEmits([
  'submit', 
  'back'
])

const refVForm = ref()

const documentDetail = ref({
    passport_number: '',
    file_document: []
})

watchEffect(fetchData)

async function fetchData() {

  if(props.migrant) {
    documentDetail.value.passport_number = props.migrant.passport_number
  }
}

const onSubmit = () => {
  refVForm.value?.validate().then(({ valid: isValid }) => {
    if (isValid) {
      emit('submit', documentDetail.value)
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
      class="mb-6"
      title="Documentos"
    >
      <VCardText>
        <VRow>
          <VCol cols="12" md="6">
            <VTextField
              v-model="documentDetail.passport_number"
              :rules="[requiredValidator, phoneValidator]"
              label="N. Documento"
              type="tel"
            />
          </VCol>
          <VCol cols="12" md="6">
            <VFileInput
              accept="image/*,application/pdf"
              v-model="documentDetail.file_document"
              value=""
              placeholder="Upload your documents"
              label="Documento (images/pdf)"
              prepend-icon="tabler-paperclip"
              >
              <template #selection="{ fileNames }">
                <template
                  v-for="fileName in fileNames"
                  :key="fileName">
                  <VChip
                    label
                    size="small"
                    variant="outlined"
                    color="primary"
                    class="me-2">
                    {{ fileName }}
                  </VChip>
                </template>
              </template>
            </VFileInput>
          </VCol>
        </VRow>
      </VCardText>
    </VCard>

    <div class="d-flex justify-end gap-x-4">
      <VBtn
        color="secondary"
        variant="tonal"
        @click="emit('back')"
      >
        Atrás
      </VBtn>
      <VBtn type="submit" color="primary">
        Siguiente
      </VBtn>
    </div>
  </VForm>
</template>
