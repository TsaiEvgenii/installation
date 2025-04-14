define([
    '../render/canvas2d',
    './config',
    './context',
    './hilight',
    '../data/helper',
    './helper/gc',
    '../ui/helper/html',
    './widgets/panel',
    './widgets/object-tree',
    './widgets/object-editor',
    './widgets/toolbar'
], function(
    Drawer, Config, Context, Hilight, DataHelper, GcHelper, HtmlHelper,
    Panel, ObjectTree, ObjectEditor, Toolbar) {

    class Editor {
        constructor(rootElement, config = {}) {
            this._rootElement = rootElement;
            this._rootWidgets = [];

            this.initContext(rootElement, config);
            this.initElements();
            this.initDrawer();
            this.initWidgets();
        }

        initContext(rootElement, config) {
            let ctx = new Context(rootElement, DataHelper.merged(Config, config));

            // Subscribe to object events
            ctx.eventManager.subscribe(this, 'object');
            ctx.eventManager.subscribe(this, 'context');

            // Object cleanup after removing commands form history
            function cleanup() {
                GcHelper.cleanup(ctx);
            }
            ctx.commandHistory.addAfterClearHandler(cleanup);
            ctx.commandHistory.addAfterRemoveHandler(cleanup);

            this._context = ctx;
        }

        initElements() {
            let ef = this._context.elementFactory;
            // Top
            let top = ef.make('div');
            HtmlHelper.addClassName(top, 'top');
            this._rootElement.appendChild(top);
            this._topElement = top;

            // Content
            let main = ef.make('div');
            HtmlHelper.addClassName(main, 'main');
            this._rootElement.appendChild(main);
            this._mainElement = main;

            // Left column
            let leftColumn = ef.make('div');
            HtmlHelper.addClassName(leftColumn, 'left-column');
            this._mainElement.appendChild(leftColumn);
            this._leftColumn = leftColumn;

            // Canvas
            let canvas = ef.make('canvas', this._context.config.General.canvas);
            this._leftColumn.appendChild(canvas);
            this._canvas = canvas;

            // Right column
            let rightColumn = ef.make('div');
            HtmlHelper.addClassName(rightColumn, 'right-column');
            this._mainElement.appendChild(rightColumn);
            this._rightColumn = rightColumn;

        }

        initDrawer() {
            let drawer = new Drawer.Drawer(this._canvas),
                hilighter = new Hilight.Hilighter(this._context),
                scale = this._context.config.General.scale,
                font = this._context.config.General.font,
                scaleMultiplier = this._context.config.General.scaleMultiplier;
            drawer.scale = scale;
            drawer.scaleMultiplier = scaleMultiplier;
            drawer.fontFamily = font ? (font.family || null) : null;
            drawer.fontSize = font ? (font.size || null) : null;
            drawer.editMode = true;
            this._context.scale = scale;
            drawer.addBeforeDraw(hilighter.hilight.bind(hilighter));
            drawer.backgroundColor = this._context.config.General.background;
            drawer.setSmoothing(this._context.config.General.smoothing);
            drawer.reset();
            this._drawer = drawer;
        }

        initWidgets() {
            let ctx = this._context;

            // Toolbar
            let toolbar = new Toolbar.Widget(ctx);
            toolbar.addTools(this._context.config.General.toolbar);
            this._topElement.appendChild(toolbar.element);
            this._rootWidgets.push(toolbar);

            // Main panel
            let main = new Panel.Widget(ctx);
            main.add(new ObjectTree.Widget(ctx));
            main.add(new ObjectEditor.Widget(ctx));
            this._rightColumn.appendChild(main.element);
            this._rootWidgets.push(main);
        }

        onEvent(event) {
            switch (event.type) {
            case 'object':
                this._onObjectEvent(event);
                break;
            case 'context':
                this._onContextEvent(event);
                break;
            }
        }

        exportData() {
            let om = this._context.objectManager;
            let objects = this._context.rootObjectIds.toArray()
                .map(om.get.bind(om))
            return this._context.dataExport.extractAll(objects);
        }
        validateData(){
            let validationResult = {
                isValid: true,
                messages: []

            };
            this.validateMeasurements(validationResult);
            return validationResult;
        }

        validateMeasurements(validationResult) {
            const duplicates = this._context._measurementManager.validateNames();
            if (duplicates.length > 0) {
                validationResult.isValid = false;
                validationResult.messages.push(`Names for measurements cannot be the same: ${duplicates.join(',')}`);
            }
        }

        importData(data) {
            let em = this._context.eventManager,
                oh = this._context.objectHelper;

            // Create objects
            let objects = this._context.dataImport.createAll(data);

            // Add root objects
            objects.forEach(function(object) {
                this._context.rootObjectIds.add(object.objectId);
            }, this);

            // Send events
            function notify(parentId, object) {
                // object event
                em.notify('object', 'added', {
                    id: object.objectId,
                    parentId: parentId
                });
                // child events
                oh.getChildren(object).forEach(
                    notify.bind(null, object.objectId));
            }
            objects.forEach(notify.bind(null, null));
        }

        clear() {
            this._context.rootObjectIds.toArray().forEach(function(objectId) {
                // destroy object tree
                let om = this._context.objectManager,
                    object = om.get(objectId);
                this._context.objectHelper.forEach(object, function(child) {
                    om.destroy(child.objectId);
                });
                // clear history
                this._context.commandHistory.clear();
                // send event
                this._context.eventManager.notify('object', 'removed', {
                    id: objectId,
                    parentId: null
                });
            }, this);
        }

        update() {
            this.redraw();
            this._rootWidgets.forEach(function(widget) { widget.update(); });
        }

        redraw() {
            let measurementManager = this._context.measurementManager;
            this._drawer.reset();
            this._context.rootObjectIds.toArray().forEach(function(objectId) {
                let object = this._context.objectManager.get(objectId);
                object.reset();
                object.prepare();
                object.place();
                measurementManager.collect(object);
                measurementManager.prepare(object);
                this._drawer.draw(object);
            }, this);
        }

        resize(width, height) {
            this._drawer.resize(width, height);
            this.redraw();
        }

        _onObjectEvent(event) {
            let objectId = event.data.id;

            // Selected objects
            switch (event.name) {
            case 'selected':
                this._context.selectedObjectIds.clear();
                this._context.selectedObjectIds.add(objectId);
                break;

            case 'added':
                if (objectId.type == 'link') {
                    this._updateLink(objectId);
                } else {
                    this._context.linkManager.updateRefObjects(objectId);
                }
                break;

            case 'removed':
                this._context.selectedObjectIds.remove(objectId);
                this._context.rootObjectIds.remove(objectId);
                break;

            case 'changed':
                if (objectId.type == 'link') {
                    this._updateLink(objectId);
                } else {
                    this._context.linkManager.updateRefObjects(objectId);
                }
                break;
            }

            this.redraw();
        }

        _updateLink(linkId) {
            let om = this._context.objectManager,
                lm = this._context.linkManager,
                link = om.get(linkId);
            lm.updateLink(link);
            lm.updateLinkObject(link);
        }

        _onContextEvent(event) {
            switch (event.name) {
            case 'changed':
                if (event.data.scale != undefined) {
                    this._drawer.scale = event.data.scale;
                    this.redraw();
                }
            }
        }

        get context() { return this._context; }
        get drawer() { return this._drawer; }
    }

    return Editor;
});
