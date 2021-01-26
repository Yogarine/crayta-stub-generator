--------------------------------------------------------------------------------------------------------
--- Similar to string, but localisable. Uses a Text type at runtime.
---
--- @shape RotationProperty : Property<Rotation>
--- @field type           "rotation"
--- @field default      Rotation|nil  @Rotation.Zero or any other value using Rotation.New(0,90,0)
--- @field stepSpeed      number|nil  @Adjusts how fast the number changes with a gamepad.
--- @field slowStepSpeed  number|nil  @As above, but when pressing the ‘slow’ modifier.
--- @field fastStepSpeed  number|nil  @As above, but when pressing the ‘fast’ modifier.
--------------------------------------------------------------------------------------------------------
local RotationProperty = {}

return RotationProperty
