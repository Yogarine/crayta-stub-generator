--------------------------------------------------------------------------------------------------------
--- Similar to string, but localisable. Uses a Text type at runtime.
---
--- @shape EntityProperty : Property<Primitive>
--- @alias EntityPropertyType "Character"|"User"|"Mesh"|"Light"|"Sound"|"Effect"|"Voxels"|"Trigger"|"Locator"|"Camera"
---
--- @field type  "entity"
--- @field is    EntityPropertyType|nil  @Require the entity to be of a specific physical type. This
---                                      doesn't apply at runtime.
--------------------------------------------------------------------------------------------------------
local EntityProperty = {}

return EntityProperty
