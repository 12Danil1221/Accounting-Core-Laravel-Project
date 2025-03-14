import Choices from 'choices.js'
import {createPopper} from '@popperjs/core'
import debounce from '../Support/Debounce.js'
import {crudFormQuery, getQueryString, prepareFormData} from '../Support/Forms.js'
import {dispatchEvents as de} from '../Support/DispatchEvents.js'
import {formToJSON} from 'axios'
import {DEFAULT_CONFIG} from 'choices.js/src/scripts/defaults'

export default (asyncUrl = '') => ({
  choicesInstance: null,
  placeholder: null,
  searchEnabled: null,
  removeItemButton: null,
  shouldSort: null,
  associatedWith: null,
  searchTerms: null,
  isLoadedOptions: false,
  customOptions: {},
  resolvedOptions: [
    'silent',
    'items',
    'choices',
    'renderChoiceLimit',
    'maxItemCount',
    'addItems',
    'addItemFilter',
    'removeItems',
    'removeItemButton',
    'editItems',
    'allowHTML',
    'duplicateItemsAllowed',
    'delimiter',
    'paste',
    'searchEnabled',
    'searchChoices',
    'searchFields',
    'searchFloor',
    'searchResultLimit',
    'position',
    'resetScrollPosition',
    'addItemFilter',
    'shouldSort',
    'shouldSortItems',
    'sorter',
    'placeholder',
    'placeholderValue',
    'searchPlaceholderValue',
    'prependValue',
    'appendValue',
    'renderSelectedChoices',
    'loadingText',
    'noResultsText',
    'noChoicesText',
    'itemSelectText',
    'uniqueItemText',
    'customAddItemText',
    'addItemText',
    'maxItemText',
    'valueComparer',
    'labelId',
    'classNames',
    'fuseOptions',
    'callbackOnInit',
    'callbackOnCreateTemplates',
  ],

  init() {
    this.placeholder = this.$el.getAttribute('placeholder')
    this.searchEnabled = !!this.$el.dataset.searchEnabled
    this.removeItemButton = !!this.$el.dataset.removeItemButton
    this.shouldSort = !!this.$el.dataset.shouldSort
    this.associatedWith = this.$el.dataset.associatedWith

    if (this.associatedWith) {
      this.$el.removeAttribute('data-associated-with')
    }

    for (const key in this.$el.dataset) {
      if (this.resolvedOptions.includes(key)) {
        this.customOptions[key] = this.$el.dataset[key]
      }
    }

    const items = []

    Array.from(this.$el.options ?? []).forEach(function (option) {
      items.push({
        label: option.text,
        value: option.value,
        selected: option.selected,
        customProperties: option.dataset?.properties ? JSON.parse(option.dataset.properties) : {},
      })
    })

    this.choicesInstance = new Choices(this.$el, {
      allowHTML: true,
      items: items,
      position: 'bottom',
      placeholderValue: this.placeholder,
      searchEnabled: this.searchEnabled,
      removeItemButton: this.removeItemButton,
      shouldSort: this.shouldSort,
      loadingText: translates?.loading ?? DEFAULT_CONFIG.loadingText,
      noResultsText: translates?.choices?.no_results ?? DEFAULT_CONFIG.noResultsText,
      noChoicesText: translates?.choices?.no_choices ?? DEFAULT_CONFIG.noChoicesText,
      itemSelectText: translates?.choices?.item_select ?? DEFAULT_CONFIG.itemSelectText,
      uniqueItemText: translates?.choices?.unique_item ?? DEFAULT_CONFIG.uniqueItemText,
      customAddItemText: translates?.choices?.custom_add_item ?? DEFAULT_CONFIG.customAddItemText,
      fuseOptions: {
        threshold: 0,
        ignoreLocation: true,
      },
      addItemText: value => {
        return (
          translates?.choices?.add_item?.replace(':value', `<b>${value}</b>`) ??
          DEFAULT_CONFIG.addItemText(value)
        )
      },
      maxItemText: maxItemCount => {
        return (
          translates?.choices?.max_item?.replace(':count', maxItemCount) ??
          DEFAULT_CONFIG.maxItemText(maxItemCount)
        )
      },
      searchResultLimit: 100,
      callbackOnCreateTemplates: function (template) {
        return {
          item: ({classNames}, data) => {
            return template(`
                <div class="${classNames.item} ${
                  data.highlighted ? classNames.highlightedState : classNames.itemSelectable
                } ${data.placeholder ? classNames.placeholder : ''}" data-item data-id="${
                  data.id
                }" data-value="${data.value}" ${data.active ? 'aria-selected="true"' : ''} ${
                  data.disabled ? 'aria-disabled="true"' : ''
                }>
                      <div class="flex gap-x-2 items-center ">
                        ${
                          data.customProperties?.image
                            ? '<div class="zoom-in h-10 w-10 overflow-hidden rounded-md">' +
                              '<img class="h-full w-full object-cover" src="' +
                              data.customProperties.image +
                              '" alt=""></div>'
                            : ''
                        }
                        <span>
                          ${data.label}
                          ${
                            this.config.removeItemButton
                              ? `<button type="button" class="choices__button choices__button--remove" data-button="">${
                                  translates?.choices?.remove_item ?? 'x'
                                }</button>`
                              : ''
                          }
                        </span>
                      </div>

                </div>
              `)
          },
          choice: ({classNames}, data) => {
            return template(`
                <div class="flex gap-x-2 items-center ${classNames.item} ${classNames.itemChoice} ${
                  data.disabled ? classNames.itemDisabled : classNames.itemSelectable
                } ${data.value == '' ? 'choices__placeholder' : ''}" data-select-text="${
                  this.config.itemSelectText
                }" data-choice ${
                  data.disabled
                    ? 'data-choice-disabled aria-disabled="true"'
                    : 'data-choice-selectable'
                } data-id="${data.id}" data-value="${data.value}" ${
                  data.groupId > 0 ? 'role="treeitem"' : 'role="option"'
                }>
                      <div class="flex gap-x-2 items-center ">
                          ${
                            data.customProperties?.image
                              ? '<div class="zoom-in h-10 w-10 overflow-hidden rounded-md">' +
                                '<img class="h-full w-full object-cover" src="' +
                                data.customProperties.image +
                                '" alt=""></div>'
                              : ''
                          }
                        <span>
                          ${data.label}
                        </span>
                      </div>
                </div>
            `)
          },
        }
      },
      callbackOnInit: () => {
        this.searchTerms = this.$el.closest('.choices').querySelector('[name="search_terms"]')

        if (asyncUrl && this.$el.dataset.asyncOnInit && !this.$el.dataset.asyncOnInitDropdown) {
          this.asyncSearch()
        }
      },
      ...this.customOptions,
    })

    this.setDataValues()

    this.$el.addEventListener(
      'change',
      () => {
        this.isLoadedOptions = false

        this.setDataValues()
      },
      false,
    )

    if (asyncUrl) {
      this.$el.addEventListener(
        'showDropdown',
        () => {
          if (!this.isLoadedOptions) {
            this.asyncSearch()
          }
        },
        false,
      )
    }

    if (this.associatedWith && asyncUrl) {
      document.querySelector(`[name="${this.associatedWith}"]`).addEventListener(
        'change',
        event => {
          this.choicesInstance.clearStore()
          this.$el.dispatchEvent(new Event('change'))
          this.isLoadedOptions = false
        },
        false,
      )
    }

    if (this.$el.dataset.overflow || this.$el.closest('.table-responsive')) {
      // Modifier "Same width" Popper reference
      const sameWidth = {
        name: 'sameWidth',
        enabled: true,
        phase: 'beforeWrite',
        requires: ['computeStyles'],
        fn: ({state}) => {
          state.styles.popper.width = `${state.rects.reference.width}px`
        },
        effect: ({state}) => {
          state.elements.popper.style.width = `${state.elements.reference.offsetWidth}px`
        },
      }

      // Create Popper on showDropdown event
      this.choicesInstance.passedElement.element.addEventListener(
        'showDropdown',
        event => {
          createPopper(
            this.choicesInstance.containerInner.element,
            this.choicesInstance.dropdown.element,
            {
              placement: 'bottom',
              strategy: 'fixed',
              modifiers: [sameWidth],
            },
          )
        },
        false,
      )
    }

    const form = this.$el.closest('form')
    if (form !== null) {
      form.addEventListener('reset', () => {
        this.choicesInstance.clearChoices()
        this.choicesInstance.setChoices(items)
        const activeItem = items.filter(item => item.selected) ?? items[0]
        this.choicesInstance.setChoiceByValue(activeItem)
      })
    }

    if (asyncUrl) {
      this.searchTerms.addEventListener(
        'input',
        debounce(event => this.asyncSearch(), 300),
        false,
      )
    }

    if (this.removeItemButton) {
      this.$el.parentElement.addEventListener('click', event => {
        if (document.activeElement.name !== 'search_terms') {
          // necessary for reactivity to work
          event.target.closest('.choices')?.querySelector('select')?.focus()
        }

        if (event.target.classList.contains('choices__button--remove')) {
          const choiceElement = event.target.closest('.choices__item')
          const id = choiceElement.getAttribute('data-id')

          if (
            this.choicesInstance._isSelectOneElement &&
            this.choicesInstance._store.placeholderChoice
          ) {
            this.choicesInstance.removeActiveItems(id)

            this.choicesInstance._triggerChange(this.choicesInstance._store.placeholderChoice.value)

            this.choicesInstance._selectPlaceholderChoice(
              this.choicesInstance._store.placeholderChoice,
            )
          } else {
            const {items} = this.choicesInstance._store

            const itemToRemove = id && items.find(item => item.id === parseInt(id, 10))

            if (!itemToRemove) {
              return
            }

            this.choicesInstance._removeItem(itemToRemove)
            this.choicesInstance._triggerChange(itemToRemove.value)
          }
        }
      })
    }
  },
  setDataValues() {
    if (this.$el.getAttribute('multiple')) {
      this.$el.setAttribute('data-choices-value', this.choicesInstance.getValue(true).join(','))
    }
  },
  morphClear(type) {
    if (type.value) {
      this.choicesInstance.clearStore()
    }
  },
  async asyncSearch() {
    const query = this.searchTerms.value ?? null
    let canRequest = this.$el.dataset.asyncOnInit || (query !== null && query.length)
    let options = []

    if (canRequest) {
      const url = asyncUrl.startsWith('/')
        ? new URL(asyncUrl, window.location.origin)
        : new URL(asyncUrl)

      url.searchParams.append('query', query)

      const form = this.$el.form
      const inputs = form ? form.querySelectorAll('[name]') : []
      let formQuery = ''

      if (inputs.length) {
        formQuery = crudFormQuery(inputs)
      }

      if (form === null) {
        formQuery = getQueryString({value: this.choicesInstance.getValue(true)})
      }

      options = await this.fromUrl(url.toString() + (formQuery.length ? '&' + formQuery : ''))
      options = options.map(item => {
        const {properties, ...other} = item
        return {
          ...other,
          customProperties: properties,
        }
      })
    }

    await this.choicesInstance.setChoices(options, 'value', 'label', true)
    this.isLoadedOptions = true
  },
  dispatchEvents(componentEvent, exclude = null, extra = {}) {
    const form = this.$el.closest('form')

    if (exclude !== '*') {
      extra['_data'] = form
        ? formToJSON(prepareFormData(new FormData(form), exclude))
        : {value: this.choicesInstance.getValue(true)}
    }

    de(componentEvent, '', this, extra)
  },
  fromUrl(url) {
    return fetch(url)
      .then(response => {
        return response.json()
      })
      .then(json => {
        return json
      })
  },
})
