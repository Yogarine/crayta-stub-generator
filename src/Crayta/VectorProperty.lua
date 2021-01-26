--------------------------------------------------------------------------------------------------------
--- Similar to string, but localisable. Uses a Text type at runtime.
---
--- @shape VectorProperty : Property<Vector>
--- @field type           "vector"
--- @field default        Vector|nil  @Vector.Zero or any other Vector value using Vector.New(1,2,3).
--- @field stepSpeed      number|nil  Adjusts how fast the number changes with a gamepad.
--- @field slowStepSpeed  number|nil  As above, but when pressing the ‘slow’ modifier.
--- @field fastStepSpeed  number|nil  As above, but when pressing the ‘fast’ modifier.
--------------------------------------------------------------------------------------------------------
local VectorProperty = {}

return VectorProperty
