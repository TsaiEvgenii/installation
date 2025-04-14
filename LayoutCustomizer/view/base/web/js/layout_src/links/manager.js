define([
    '../object/id'
], function(ObjectId) {

    class Manager {
        constructor(objectManager) {
            this._objectManager = objectManager
            // reference ID -> [link1, link2, ...]
            this._refMap = new ObjectId.Map();
            // link ID -> reference ID
            this._linkMap = new ObjectId.Map();
        }

        updateLink(link) {
            let linkId = link.objectId,
                refId = link.ref,
                oldRefId = this._linkMap.get(linkId);

            // Remove link from old reference list
            if (oldRefId) {
                let list = this._refMap.get(oldRefId);
                if (list) {
                    list.remove(linkId);
                }
            }

            if (refId) {
                // Add link to new reference list
                let list = (this._refMap.get(refId) || new ObjectId.Set());
                list.add(link.objectId);
                this._refMap.set(refId, list);

                // Add link ID -> refId
                this._linkMap.set(linkId, refId.copy());
            }
        }

        updateAllObjects() {
            this._refMap.keys().forEach(function(refId) {
                this.updateRefObjects(refId);
            }, this);
        }

        updateRefObjects(refId, field = null) {
            let list = this._refMap.get(refId);
            if (list) {
                let om = this._objectManager,
                    ref = om.get(refId);
                list.toArray().forEach(function(linkId) {
                    let link = om.get(linkId);
                    if (field === null || link.name == field) {
                        // Copy reference value to link parent
                        link.parent[link.name] = ref[link.name];
                    }
                });
            }
        }

        updateLinkObject(link) {
            let refId = link.ref;
            if (refId) {
                let ref = this._objectManager.get(refId);
                // Copy reference value to link parent
                link.parent[link.name] = ref[link.name];
            }
        }

        hasObjectId(objId) {
            let result = false;
            if(this._linkMap._map.link) {
                let arrayIds = Object.keys(this._linkMap._map.link).map(i => this._linkMap._map.link[i]);
                arrayIds.forEach(function (linkObjId) {
                    if (linkObjId.isSame(objId)) {
                        result = true;
                        return false;
                    }
                })
            }
            return result;
        }
    }

    return Manager;
});
