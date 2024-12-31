document.addEventListener('alpine:init', () => {
  Alpine.data('recipe', (ingredientLists = [], servings = 1) => ({
    init() {
      this.$el.querySelectorAll('.recipe-instructions ol li').forEach(li => {
        const label = document.createElement('label');
        label.innerHTML = '<svg viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" data-svg="check"><polyline fill="none" stroke="currentColor" stroke-width="1.1" points="4,10 8,15 17,4"></polyline></svg>';
        label.classList.add('recipe-instructions-step-checkbox', 'button', 'button-icon', 'button-outline');

        const checkbox = document.createElement('input');
        checkbox.type = 'checkbox';
        checkbox.style.display = 'none';

        label.appendChild(checkbox);
        li.insertBefore(label, li.firstChild);
      });
    },

    ingredientLists,
    servings,
    servingsText() {
      return this.servings + ' ' + (this.servings === 1 ? 'portie' : 'porties');
    },
    updateServings(amount) {
      amount = parseInt(amount);
      for (let listKey in this.ingredientLists) {
        for (let key in this.ingredientLists[listKey].ingredients) {
          const ingredientAmount = parseFloat(this.ingredientLists[listKey].ingredients[key].amount);
          // Round amount to 2 decimals
          this.ingredientLists[listKey].ingredients[key].amount = (ingredientAmount / this.servings) * amount;
        }
      }
      this.servings = amount;
    },

    strikedIngredientsList: new Set(),
    strikeIngredient(ingredient) {
      if (this.strikedIngredientsList.has(ingredient)) {
        this.strikedIngredientsList.delete(ingredient);
      } else {
        this.strikedIngredientsList.add(ingredient);
      }
    },
  }));
});
