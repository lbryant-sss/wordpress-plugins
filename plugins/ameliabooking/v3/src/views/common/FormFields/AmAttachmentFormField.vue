<template>
  <el-form-item
    :id="props.id"
    ref="formFieldRef"
    class="am-ff__item"
    :prop="props.itemName"
    :label-position="props.labelPosition"
  >
    <template #label>
      <span class="am-ff__item-label" v-html="props.label" />
    </template>
    <AmAttachment
      :id="props.id"
      v-model="model"
      :auto-upload="false"
      @change="onAddFile"
      @remove="onRemoveFile"
    >
      {{props.btnLabel}}
    </AmAttachment>
  </el-form-item>
</template>

<script setup>
// * _components
import AmAttachment from '../../_components/attachment/AmAttachment.vue'

// * Import from Vue
import {
  computed,
  ref,
  toRefs
} from "vue";

// * Form Item Props
let props = defineProps({
  modelValue: {
    type: [String, Array, Object, Number],
    required: true
  },
  id: {
    type: [String, Number]
  },
  itemName: {
    type: String,
    required: true
  },
  label: {
    type: String
  },
  labelPosition: {
    type: String,
    default: 'top'
  },
  btnLabel: {
    type: String
  }
})

// * Define Emits
const emits = defineEmits(['update:modelValue', 'change', 'remove'])

// * Component model
let { modelValue } = toRefs(props)
let model = computed({
  get: () => modelValue.value,
  set: (val) => {
    emits('update:modelValue', val)
  }
})

// * Form Item Reference
let formFieldRef = ref(null)

defineExpose({
  formFieldRef
})

function onAddFile (a) {
  emits('update:modelValue', a.raw)
}

function onRemoveFile (a) {
  emits('update:modelValue', a.raw)
}
</script>

<script>
export default {
  name: "AttachmentFormField"
}
</script>
