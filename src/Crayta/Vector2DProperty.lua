--------------------------------------------------------------------------------------------------------
--- Similar to string, but localisable. Uses a Text type at runtime.
---
--- @shape Vector2DProperty : Property<Vector2D>
--- @field type           "vector2d"
--- @field default      Vector2D|nil  Vector2D.Zero or any other Vector2D value using Vector2D.New(1,2)
--- @field stepSpeed      number|nil  Adjusts how fast the number changes with a gamepad.
--- @field slowStepSpeed  number|nil  As above, but when pressing the ‘slow’ modifier.
--- @field fastStepSpeed  number|nil  As above, but when pressing the ‘fast’ modifier.
--------------------------------------------------------------------------------------------------------
local Vector2DProperty = {}

return Vector2DProperty
