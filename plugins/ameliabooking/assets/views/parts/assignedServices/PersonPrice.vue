<template>
  <!-- Person Price -->
  <div class="am-service-custom-duration">
    <div>
      <el-row :gutter="24" class="zero-margin-bottom">
        <el-col :span="6">
          <span>{{$root.labels.from + ':'}}</span>
        </el-col>
        <el-col :span="6">
          <span>{{$root.labels.to_upper + ':'}}</span>
        </el-col>
        <el-col :span="10">
          <span>{{$root.labels.price + ':'}}</span>
        </el-col>
        <el-col :span="2">
        </el-col>
      </el-row>
      <el-row
        :gutter="24"
        type="flex"
        v-for="(item, index) in service.customPricing.persons"
        v-if="enabledRange || !index || service.customPricing.persons[index - 1].range + 1 <= service.maxCapacity"
        :key="index"
        class="small-margin-bottom am-service-custom-duration-item"
      >
        <el-col :span="6" style="margin-bottom: 7px;">
          <el-select
            v-if="enabledRange"
            :value="!index ? 1 : service.customPricing.persons[index - 1].range + 1"
            placeholder=""
            :disabled="true"
          >
          </el-select>
          <el-input-number
            v-else
            :controls=false
            :value="!index ? 1 : service.customPricing.persons[index - 1].range + 1"
            style=""
            :disabled="true"
          />
        </el-col>
        <el-col :span="6" style="margin-bottom: 7px;">
          <el-select
            v-if="enabledRange"
            v-model="item.range"
            :placeholder="service.maxCapacity.toString()"
            @change="personsSelected(index)"
          >
            <el-option
              v-for="n in service.maxCapacity"
              :key="n"
              :disabled="isRangeSelected(n, index)"
              :value="n"
            >
            </el-option>
          </el-select>
          <el-input-number
            v-else
            :controls=false
            :value="service.maxCapacity <= item.range || index === service.customPricing.persons.length - 1 ? service.maxCapacity : item.range"
            style=""
            :disabled="true"
          />
        </el-col>
        <el-col :span="10" style="margin-bottom: 7px;">
          <money
            v-model="item.price"
            v-bind="moneyComponentData"
            @input="index === 0 ? service.price = item.price : false"
            class="el-input el-input__inner"
            style="height: 40px;"
          >
          </money>
        </el-col>
        <el-col v-if="Object.keys(service.customPricing.persons).length > 2" :span="2" style="margin-bottom: 7px;">
          <span @click="deletePersonsRange(index)" v-if="enabledDelete && service.customPricing.persons.length > 1">
            <img class="svg-amelia" width="16px" :src="$root.getUrl + 'public/img/delete.svg'">
          </span>
        </el-col>
      </el-row>
    </div>
  </div>
  <!-- /Person Price -->
</template>

<script>
import priceMixin from '../../../js/common/mixins/priceMixin'
import { Money } from 'v-money'

export default {

  mixins: [
    priceMixin
  ],

  name: 'PersonPrice',

  props: {
    service: {},
    enabledDelete: true,
    enabledRange: true
  },

  methods: {
    personsSelected (index) {
      if (this.service.customPricing.persons.length - 1 === index &&
        this.service.customPricing.persons[this.service.customPricing.persons.length - 1].range !== this.service.maxCapacity
      ) {
        this.addRange()
      }
    },

    isRangeSelected (n, index) {
      if (this.service.customPricing.persons.length === 1) {
        return false
      } else if (index === 0) {
        return n >= (this.service.customPricing.persons[index + 1].range ? this.service.customPricing.persons[index + 1].range : this.service.maxCapacity)
      } else if (index === this.service.customPricing.persons.length - 1) {
        return n <= this.service.customPricing.persons[index - 1].range
      } else {
        return n >= (this.service.customPricing.persons[index + 1].range ? this.service.customPricing.persons[index + 1].range : this.service.maxCapacity) || n <= this.service.customPricing.persons[index - 1].range
      }
    },

    addRange (value = null) {
      this.service.customPricing.persons.push({range: value !== null ? value : null, price: 0, rules: []})
    },

    deletePersonsRange (index) {
      if (this.service.customPricing.persons.length - 1 === index) {
        this.service.customPricing.persons[index - 1].range = null
      }

      this.service.customPricing.persons.splice(index, 1)

      if (this.service.customPricing.persons.length <= 1) {
        this.service.customPricing.enabled = null

        this.service.customPricing.persons = []

        this.$emit('disable')
      }
    }
  },

  mounted () {
    if (!this.service.customPricing.persons.length) {
      this.addRange(1)
      this.addRange()
    }
  },

  components: {
    Money
  }
}
</script>
