// resources/js/utils/date.js
export function formatDateUS(dateStr) {
  // dateStr esperado: "YYYY-MM-DD"
  if (!dateStr) return ''
  const parts = dateStr.split('-')
  if (parts.length !== 3) return ''
  const [year, month, day] = parts
  return `${month}/${day}/${year}` // MM/DD/YYYY
}
