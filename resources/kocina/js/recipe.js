document.addEventListener('alpine:init', () => {

  Alpine.data('recipe', (ingredientLists = [], servings = 1) => {
    return {
      ingredientLists,
      servings,
      servingsText() {
        return this.servings + ' ' + (this.servings === 1 ? 'portie' : 'porties');
      },
      incrementServings() {
        for (let listKey in this.ingredientLists) {
          for (let key in this.ingredientLists[listKey].ingredients) {
            let amount = parseFloat(this.ingredientLists[listKey].ingredients[key].amount);
            // round amount to 2 decimals
            this.ingredientLists[listKey].ingredients[key].amount = amount + amount / parseFloat(this.servings);
          }
        }
        this.servings++;
      },

      decrementServings() {
        for (let listKey in this.ingredientLists) {
          for (let key in this.ingredientLists[listKey].ingredients) {
            let amount = parseFloat(this.ingredientLists[listKey].ingredients[key].amount);
            this.ingredientLists[listKey].ingredients[key].amount = amount - amount / parseFloat(this.servings);
          }
        }
        this.servings--;
      },
    };
  });
});
