--------------------------------------------------------------------------------------------------------
--- Similar to string, but localisable. Uses a Text type at runtime.
---
--- @shape TextProperty : Property<Text>
--- @field type     "text"
--- @field default  string|nil
--- @field options  StringPropertyOptions|nil  @A list of allowed values, either named:
---                                            {Monday = “Mon”, Tuesday = “Tue”, Wednesday = “Wed”},
---                                            or unnamed: {“Monday”, “Tuesday”, “Wednesday”}
--------------------------------------------------------------------------------------------------------
local TextProperty = {}

return TextProperty
