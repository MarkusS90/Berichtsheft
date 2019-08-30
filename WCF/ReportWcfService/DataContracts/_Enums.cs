using LinqToDB.Mapping;

namespace ReportWcfService
{
    public enum UserTypes
    {
        [MapValue("apprentice")]
        Apprentice,
        [MapValue("instructor")]
        Instructor,
        [MapValue("ihk")]
        Ihk
    };

    public enum DaysOfWeek
    {
        Monday,
        Tuesday,
        Wednesday,
        Thursday,
        Friday
    }
}