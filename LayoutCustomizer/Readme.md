Main module that was created to allow manage windows/doors in UI
----
**Features:**
 * ...
 * Remove `section_sizes` attribute from order items emails
 * https://app.asana.com/0/1177395662263354/1197504336481685/f - convert prices
 

 **Tips:**
 * `view/base/web/js/layout_src` folder contains all original js files
 * `view/base/web/js/layout` folder contains all converted to es5 files (Do not change them!)
 * `view/base/web/js/polyfills.js` file contain all polyfills for IE11. Please, add new polyfills here in case js functions don't work in IE.
 
 **! Don't forget to use `gulp build` to compile all the js source files**

---

**Create CSV file with Layout info. 'Height' and 'Width' fields should contain the canvas root block values**  
https://app.asana.com/0/1175739832816981/1199545523850005/f  
https://youtrack.belvgdev.com/issue/SD-976  
`bin/magento belvg:layout:get_wrapper_height_width`  

----

**Task**: Fix AJAX response after Add To Cart
https://app.asana.com/0/1193006953339046/1199538019072590/f

**Description**:

Request /customer/section/load after "Add to cart" has subtotalAmount without take into account countdown.
The active countdown discount percent should be considered to the calculation here.
