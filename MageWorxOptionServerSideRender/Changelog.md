1. Add mixin to BelVG_MageWorxUrls/js/catalog/product/isDefault-override, for disabling first run app/code/BelVG/MageWorxOptionServerSideRender/view/frontend/web/js/catalog/product/isDefault-override-mixin.js:16

            if (this.options.router != 'checkout') {
                //@todo renderSize optimization
                // this.processFirstRun(base);
            }
        },
2. Add mixin app/code/BelVG/MageWorxOptionServerSideRender/view/frontend/web/js/catalog/product/url-managment-mixin.js for disabling handleFirstRun function.
3. Use ContextFactory instead Context this changes helps to use some services not only for rendering price @see \BelVG\FixFacebookPixelProductPrice\Override\FacebookPixel\Helper\Data::getFinalPrice