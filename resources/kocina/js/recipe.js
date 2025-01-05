document.addEventListener('alpine:init', () => {
  Alpine.data('recipe', (ingredientLists = [], servings = 1) => ({
    async init() {
      this.initInstructionStepCheckboxes();
      await this.initWakeLock();
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

    initInstructionStepCheckboxes() {
      this.$refs.instructions.querySelectorAll('ol li').forEach(li => {
        const label = document.createElement('label');
        // TODO Use SVG icon from assets
        label.innerHTML = '<svg viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" data-svg="check"><polyline fill="none" stroke="currentColor" stroke-width="1.1" points="4,10 8,15 17,4"></polyline></svg>';
        label.classList.add('recipe-instructions-step-checkbox', 'button', 'button-icon', 'button-outline');

        const checkbox = document.createElement('input');
        checkbox.type = 'checkbox';
        checkbox.style.display = 'none';

        label.appendChild(checkbox);
        li.insertBefore(label, li.firstChild);
      });
    },

    wakeLock: undefined,
    isWakeLockAvailable: false,
    isWakeLockEnabled: false,
    // TODO Could this become a performance issue? Because it's always loaded. Maybe load async?
    async initWakeLock() {
      if (!("wakeLock" in navigator)) {
        return;
      }

      this.isWakeLockAvailable = true;

      document.addEventListener("visibilitychange", async () => {
        if (this.wakeLock && document.visibilityState === "visible") {
          console.log('Screen wake lock visibilitychange event fired');

          this.wakeLock = await navigator.wakeLock.request("screen");
        }
      });
    },

    async toggleWakeLock() {
      if (this.wakeLock) {
        this.wakeLock.release().then(() => {
          this.wakeLock = undefined;
        }).catch((err) => {
          console.error(`Wake lock release error: ${err.name}, ${err.message}`);
        });
      } else {
        try {
          this.wakeLock = await navigator.wakeLock.request("screen");
        } catch (err) {
          // The Wake Lock request has failed - usually system related, such as battery.
          console.error(`Wake lock request error: ${err.name}, ${err.message}`);
        }
      }

      this.isWakeLockEnabled = !this.isWakeLockEnabled;
    }
  }));
});
