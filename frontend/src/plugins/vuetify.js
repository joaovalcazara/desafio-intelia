import 'vuetify/styles'
import { createVuetify } from 'vuetify'
import { pt, en, es } from 'vuetify/locale'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'

const getInitialLocale = () => {
  try {
    const saved = localStorage.getItem('locale')
    if (saved && ['pt', 'en', 'es'].includes(saved)) return saved
  } catch (e) {}
  try {
    const nav = navigator && navigator.language ? navigator.language.split('-')[0] : null
    if (nav && ['pt', 'en', 'es'].includes(nav)) return nav
  } catch (e) {}
  return 'pt'
}

const initialLocale = getInitialLocale()

export default createVuetify({
  components,
  directives,
  locale: {
    locale: initialLocale,
    fallback: 'pt',
    messages: { pt, en, es }
  }
})
