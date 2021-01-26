--------------------------------------------------------------------------------------------------------
--- @shape NumberProperty : Property<number>
--- @alias NumberPropertyOptions table<string,number>|number[]
--- @alias NumberPropertyEditor  "int"|"float"|"seconds"|"days"|"slider"
---
--- @field type                   "number"
--- @field default                number|nil
--- @field min                    number|nil  @Sets the min value that can be entered into the editor UI.
---                                           Doesn't apply at runtime.
--- @field max                    number|nil  @Sets the max value that can be entered into the editor UI.
---                                           Doesn't apply at runtime.
--- @field stepSpeed              number|nil  @Adjusts how fast the number changes with a gamepad.
--- @field slowStepSpeed          number|nil  @Adjusts how fast the number changes with a gamepad.
--- @field allowFloatingPoint    boolean|nil  @allow/disallow decimal numbers in the editor UI. Doesn’t
---                                           apply at runtime.
--- @field options NumberPropertyOptions|nil  @a list of allowed values, either named:
---                                           {Slow = 0.25, Normal = 1, Fast = 2, Ludicrous = 100},
---                                           or unnamed:
---                                           {0.25, 1, 2, 100}
--- @field editor   NumberPropertyEditor|nil  @Changes the widget displayed in the editor.
--------------------------------------------------------------------------------------------------------
local NumberProperty = {}

return NumberProperty
