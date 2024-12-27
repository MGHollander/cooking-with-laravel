document.addEventListener('alpine:init', () => {
  Alpine.data('recipe', (ingredientLists = [], servings = 1) => ({
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
