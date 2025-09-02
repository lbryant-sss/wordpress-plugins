<template>
  <!-- Period Price -->
  <div class="am-service-periods" style="padding-bottom: 10px;">
    <el-tabs v-model="mode">
      <el-tab-pane v-if="service.customPricing.periods.default.length || enabledAdd" name="default" :label="$root.labels.week_days"></el-tab-pane>
      <el-tab-pane v-if="service.customPricing.periods.custom.length || enabledAdd" name="custom" :label="$root.labels.custom_days"></el-tab-pane>
    </el-tabs>

    <div v-for="(item, dayIndex) in service.customPricing.periods[mode]"
         class="am-service-periods-day"
    >
      <el-row v-if="mode === 'default'" style="margin-top: 15px;">
        <el-col :sm="12" :md="20">
          <el-checkbox-group
            v-if="mode === 'default'"
            v-model="item.days"
            :class="{'am-service-periods-day-disabled': !enabledEdit}"
            :border="true"
            size="mini"
            style="height: 45px;"
          >
            <el-checkbox-button
              v-for="(weekDay, weekDayIndex) in weekDays"
              :label="weekDayIndex"
              :key="weekDayIndex"
              :disabled="service.customPricing.periods[mode].reduce((selected, obj, index) => selected.concat(dayIndex !== index ? obj.days : []), []).indexOf(weekDayIndex) !== -1"
            >
              {{weekDay.label}}
            </el-checkbox-button>
          </el-checkbox-group>
        </el-col>

        <el-col :sm="12" :md="4">
          <div v-if="enabledDelete" class="am-service-periods-day-remove">
            <img @click="deleteDays(dayIndex)" class="svg-amelia" width="16px" :src="$root.getUrl + 'public/img/delete.svg'">
          </div>
        </el-col>
      </el-row>

      <el-row v-if="mode === 'custom'" style="margin-top: 15px;">
        <el-col :sm="12" :md="20" class="v-calendar-column">
          <v-date-picker
            v-model="item.dates"
            mode='range'
            style="margin-bottom: 16px; height: 45px;"
            input-class="el-input__inner"
            popover-visibility="focus"
            popover-direction="bottom"
            :placeholder="$root.labels.pick_a_date_or_range"
            :popover-align="'center'"
            :show-day-popover=false
            :is-expanded=false
            :is-required=false
            :is-double-paned="false"
            :is-read-only=true
            :tint-color='"#1A84EE"'
            :input-props='{class: "el-input__inner", readOnly: "readonly"}'
            :formats="vCalendarFormats"
            :disabled-dates="disabledDates(dayIndex)"
            :class="{'am-service-periods-day-disabled': !enabledEdit}"
          >
            <template v-slot="{ inputValue, inputEvents }">
              <input
                class="el-input__inner"
                :value="item.dates.start && item.dates.end ? getFrontedFormattedDate($moment(item.dates.start).format('YYYY-MM-DD')) + ' - ' + getFrontedFormattedDate($moment(item.dates.end).format('YYYY-MM-DD')) : ''"
                :placeholder="momentDateFormat + ' - ' + momentDateFormat"
                v-on="inputEvents"
              />
            </template>
          </v-date-picker>
        </el-col>

        <el-col :sm="12" :md="4">
          <div v-if="enabledDelete" class="am-service-periods-day-remove">
            <img @click="deleteDays(dayIndex)" class="svg-amelia" width="16px" :src="$root.getUrl + 'public/img/delete.svg'">
          </div>
        </el-col>
      </el-row>

      <el-row :gutter="10" v-for="(range, rangeIndex) in item.ranges" :key="rangeIndex" class="am-service-periods-day-time">
        <!-- Pricing Start -->
        <el-col :md="7">
          <el-time-select
            v-model="range.from"
            :disabled="!enabledEdit"
            :clearable="false"
            placeholder="00:00"
            :picker-options="getTimeSelectOptionsWithLimits(rangeIndex === 0 ? null : getPreviousSlot(item.ranges[rangeIndex - 1].to), range.to)"
            size="medium"
          >
          </el-time-select>
        </el-col>

        <!-- Pricing End -->
        <el-col :md="7">
          <el-time-select
            v-model="range.to"
            :disabled="!enabledEdit"
            :clearable="false"
            placeholder="24:00"
            :picker-options="getTimeSelectOptionsWithLimits(range.from, rangeIndex === item.ranges.length - 1 ? null : getNextSlot(item.ranges[rangeIndex + 1].from))"
            size="medium"
          >
          </el-time-select>
        </el-col>

        <el-col :md="10">
          <money
            v-model="range.price"
            v-bind="moneyComponentData"
            class="el-input el-input__inner"
            style="width: 70%;"
          >
          </money>

          <div v-show="enabledDelete && service.customPricing.periods[mode][dayIndex].ranges.length > 1"
               @click="deletePeriod(dayIndex, rangeIndex)"
               class="am-service-periods-day-time-remove"
               style="margin-bottom: auto; vertical-align: text-bottom;"
          >
            <i class="el-icon-close remove"></i>
          </div>
        </el-col>
      </el-row>

      <el-button
        v-if="enabledAdd"
        type="primary"
        size="mini"
        :class="{'am-service-periods-day-time-remove-disabled': !item.ranges[item.ranges.length - 1].to || item.ranges[item.ranges.length - 1].to === '24:00'}"
        @click="addPeriod(dayIndex)"
        :disabled="!item.ranges[item.ranges.length - 1].to || item.ranges[item.ranges.length - 1].to === '24:00'"
      >
        {{ $root.labels.add_period }}
      </el-button>
    </div>

    <div v-if="enabledAdd"
         class="am-service-periods-add"
    >
      <el-button
        type="primary"
        size="mini"
        :class="{'am-service-periods-add-disabled': mode === 'default' ? service.customPricing.periods[mode].reduce((sum, obj) => sum + obj.days.length, 0) === 7 : false}"
        :disabled="mode === 'default' ? service.customPricing.periods[mode].reduce((sum, obj) => sum + obj.days.length, 0) === 7 : false"
        @click="mode === 'default' ? addDefaultDays() : addCustomDays()"
      >
        {{ $root.labels.add_days }}
      </el-button>
    </div>
  </div>
  <!-- /Period Price -->
</template>

<script>
import priceMixin from '../../../js/common/mixins/priceMixin'
import dateMixin from '../../../js/common/mixins/dateMixin'
import periodMixin from '../../../js/backend/mixins/periodMixin'
import durationMixin from '../../../js/common/mixins/durationMixin'
import { Money } from 'v-money'

export default {

  mixins: [
    dateMixin,
    durationMixin,
    periodMixin,
    priceMixin
  ],

  name: 'PeriodPrice',

  props: {
    service: {},
    enabledAdd: true,
    enabledEdit: true,
    enabledDelete: true
  },

  data () {
    return {
      mode: 'default',
      periods: [],
      weekDays: []
    }
  },

  methods: {
    addPeriod (index) {
      this.service.customPricing.periods[this.mode][index].ranges.push({
        from: this.service.customPricing.periods[this.mode][index].ranges[this.service.customPricing.periods[this.mode][index].ranges.length - 1].to,
        to: null,
        price: 0
      })
    },

    addDefaultDays () {
      let occupied = []

      this.service.customPricing.periods[this.mode].forEach(i => {
        occupied = occupied.concat(i.days)
      })

      this.service.customPricing.periods[this.mode].push({
        days: [0, 1, 2, 3, 4, 5, 6].filter(i => !occupied.includes(i)),
        ranges: [{from: null, to: null, price: 0}]
      })
    },

    addCustomDays () {
      this.service.customPricing.periods[this.mode].push({
        dates: {start: '', end: ''},
        ranges: [{from: null, to: null, price: 0}]
      })
    },

    deleteDays (index) {
      this.service.customPricing.periods[this.mode].splice(index, 1)

      if (this.service.customPricing.periods.default.length === 0 && this.service.customPricing.periods.custom.length) {
        this.mode = 'custom'
      } else if (this.service.customPricing.periods.custom.length === 0 && this.service.customPricing.periods.default.length) {
        this.mode = 'default'
      }
    },

    deletePeriod (dayIndex, rangeIndex) {
      this.service.customPricing.periods[this.mode][dayIndex].ranges.splice(rangeIndex, 1)
    },

    getPreviousSlot (time) {
      return this.getSecondsInStringTime(this.getStringTimeInSeconds(time) - this.$root.settings.general.timeSlotLength)
    },

    getNextSlot (time) {
      return this.getSecondsInStringTime(this.getStringTimeInSeconds(time) + this.$root.settings.general.timeSlotLength)
    },

    disabledDates (dayIndex) {
      let selectedRangeDates = []

      if (this.service) {
        this.service.customPricing.periods.custom.forEach((item, index) => {
          if (dayIndex !== index && item.dates.start && item.dates.end) {
            let rangeStartDate = this.$moment(item.dates.start)

            let rangeEndDate = this.$moment(item.dates.end)

            let periodDates = []

            while (rangeStartDate.isSameOrBefore(rangeEndDate)) {
              periodDates.push(this.$moment(rangeStartDate).toDate())

              rangeStartDate.add(1, 'days')
            }

            selectedRangeDates = selectedRangeDates.concat(periodDates)
          }
        })
      }

      return selectedRangeDates
    }
  },

  created () {
    for (let i = 0; i < 7; i++) {
      this.weekDays.push({
        label: this.$moment().isoWeekday(i + 1).format('dd'),
        enabled: true
      })
    }

    if (!this.service.customPricing.periods.default.length && this.service.customPricing.periods.custom.length) {
      this.mode = 'custom'
    }
  },

  components: {
    Money
  }
}
</script>
