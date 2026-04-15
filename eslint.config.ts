import { defineConfigWithVueTs, vueTsConfigs } from '@vue/eslint-config-typescript'
import pluginVue from 'eslint-plugin-vue'
import skipFormatting from '@vue/eslint-config-prettier/skip-formatting'

export default defineConfigWithVueTs(
    // 1. Игноры
    {
        ignores: ['**/dist/**', '**/dist-ssr/**', '**/coverage/**'],
    },

    // 2. Базовые конфиги Vue (Essential)
    // Используем spread (...), так как это массив объектов конфигурации
    ...pluginVue.configs['flat/essential'],

    // 3. Конфиги TypeScript
    // В зависимости от версии это может быть объект или массив.
    // Recommended обычно поставляется как массив во Flat Config.
    vueTsConfigs.recommended,

    // 4. Твои правила
    {
        name: 'app/files-to-lint',
        files: ['**/*.{ts,mts,tsx,vue}'],
        rules: {
            'no-multiple-empty-lines': ['error', {
                'max': 2,
                'maxEOF': 0,
                'maxBOF': 0
            }],
            'arrow-parens': ['error', 'as-needed'],
            'max-len': ['error', {
                'code': 150,
                'ignoreUrls': true,
                'ignoreStrings': true,
                'ignoreTemplateLiterals': true
            }],
            // '@typescript-eslint/no-explicit-any': ['warn', {
            //     ignoreRestArgs: true,
            //     // или вообще отключить:
            //     // "off"
            // }],
        },
    },

    // 5. Пропускаем форматирование
    skipFormatting
)
