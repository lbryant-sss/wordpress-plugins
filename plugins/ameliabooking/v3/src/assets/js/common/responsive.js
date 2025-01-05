function useResponsiveClass (width) {
  // rw - responsive width
  if (width <= 320) return 'am-rw-768 am-rw-650 am-rw-600 am-rw-500 am-rw-480 am-rw-420 am-rw-400 am-rw-380 am-rw-360 am-rw-320'
  if (width <= 360) return 'am-rw-768 am-rw-650 am-rw-600 am-rw-500 am-rw-480 am-rw-420 am-rw-400 am-rw-380 am-rw-360'
  if (width <= 380) return 'am-rw-768 am-rw-650 am-rw-600 am-rw-500 am-rw-480 am-rw-420 am-rw-400 am-rw-380'
  if (width <= 400) return 'am-rw-768 am-rw-650 am-rw-600 am-rw-500 am-rw-480 am-rw-420 am-rw-400'
  if (width <= 420) return 'am-rw-768 am-rw-650 am-rw-600 am-rw-500 am-rw-480 am-rw-420'
  if (width <= 480) return 'am-rw-768 am-rw-650 am-rw-600 am-rw-500 am-rw-480'
  if (width <= 500) return 'am-rw-768 am-rw-650 am-rw-600 am-rw-500'
  if (width <= 600) return 'am-rw-768 am-rw-650 am-rw-600'
  if (width <= 650) return 'am-rw-768 am-rw-650'
  if (width <= 768) return 'am-rw-768'
  return ''
}

export {
  useResponsiveClass
}