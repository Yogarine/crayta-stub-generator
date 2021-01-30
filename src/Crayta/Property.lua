--------------------------------------------------------------------------------------------------------
--- @alias PropertyValue number|string|Text|boolean|Vector|Vector2D|Rotation|Primitive|Color|Event|Asset
--------------------------------------------------------------------------------------------------------

--------------------------------------------------------------------------------------------------------
--- @shape Property<T : PropertyValue>
--- @field name                           string      @The property name that will appear in the editor.
--- @field tooltip                        string|nil  @A tooltip message that appears when hovering over
---                                                   the element in editor.
--- @field editable                      boolean|nil  @False hides the property in the editor UI. Useful
---                                                   if you want a property just for replicating data
---                                                   to clients, and want to keep the UI tidy by
---                                                   preventing the user from being able to see/edit
---                                                   it.
--- @field editableInBasicMode           boolean|nil  False hides the property, but only in basic mode.
--- @field container                  ""|"array"|nil  @Setting this to “array” will present the property
---                                                   as an array of the given type, allowing the user
---                                                   to add and remove values. Each array element, and
---                                                   the “length” field is inherited from a template
---                                                   individually.
--- @field visibleIf  fun(properties:Properties)|nil  @This function should return true when the
---                                                   property should be shown on the UI, false to hide
---                                                   it. Useful for keeping the UI tidy by hiding
---                                                   irrelevant options. The function's properties
---                                                   parameter provides access to the other properties
---                                                   (just as you would access with self.properties in
---                                                   a script):
---
---                                                      function(properties) return
---                                                         properties.allowRespawns == true
---                                                      end
--------------------------------------------------------------------------------------------------------
local Property = {}

return Property
